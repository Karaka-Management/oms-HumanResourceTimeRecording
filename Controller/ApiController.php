<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\HumanResourceTimeRecording
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\HumanResourceTimeRecording\Controller;

use Modules\Admin\Models\AccountMapper;
use Modules\Admin\Models\NullAccount;
use Modules\HumanResourceManagement\Models\EmployeeMapper;
use Modules\HumanResourceTimeRecording\Models\ClockingStatus;
use Modules\HumanResourceTimeRecording\Models\ClockingType;
use Modules\HumanResourceTimeRecording\Models\PermissionCategory;
use Modules\HumanResourceTimeRecording\Models\Session;
use Modules\HumanResourceTimeRecording\Models\SessionElement;
use Modules\HumanResourceTimeRecording\Models\SessionElementMapper;
use Modules\HumanResourceTimeRecording\Models\SessionMapper;
use phpOMS\Account\PermissionType;
use phpOMS\DataStorage\Database\Query\OrderType;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;

/**
 * HumanResourceTimeRecording controller class.
 *
 * @package Modules\HumanResourceTimeRecording
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class ApiController extends Controller
{
    /**
     * Api method to create a session
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSessionCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!$this->app->accountManager->get($request->header->account)->hasPermission(
            PermissionType::CREATE, $this->app->unitId, $this->app->appId, self::NAME, PermissionCategory::SESSION_FOREIGN
        )) {
            $response->header->status = RequestStatusCode::R_403;
            $this->createInvalidCreateResponse($request, $response, []);

            return;
        }

        $account = EmployeeMapper::get()
            ->with('profile')
            ->where('profile/account', $request->getDataInt('account') ?? $request->header->account)
            ->execute();

        if ($account->id === 0) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, []);

            return;
        }

        $session = $this->createSessionFromRequest($request);
        $this->createModel($request->header->account, $session, SessionMapper::class, 'session', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $session);
    }

    /**
     * Method to create a session from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Session
     *
     * @since 1.0.0
     */
    private function createSessionFromRequest(RequestAbstract $request) : Session
    {
        $account = $request->getDataInt('account') ?? $request->header->account;

        $session       = new Session(new NullAccount($account));
        $session->type = ClockingType::tryFromValue($request->getDataInt('type')) ?? ClockingType::OFFICE;

        // a custom datetime can only be set if the user is allowed to create a session for a foreign account or if the session is a vacation
        $dt = $request->hasData('account') || $session->type === ClockingType::VACATION
            ? ($request->getDataDateTime('datetime') ?? new \DateTime('now'))
            : new \DateTime('now');

        $element         = new SessionElement($session, $dt);
        $element->status = ClockingStatus::tryFromValue($request->getDataInt('status')) ?? ClockingStatus::START;

        $session->sessionElements[] = $element;
        $session->recalculate();

        return $session;
    }

    /**
     * Api method to create a session element
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSessionElementCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!$request->hasData('session') &&
            ($request->getDataInt('status') ?? -1) === ClockingStatus::START
        ) {
            $this->apiSessionCreate($request, $response);

            return;
        }

        /*
        if (!empty($val = $this->validateSessionElementCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }
        */

        if ($request->hasData('account')
            && $request->getDataInt('account') !== $request->header->account
            && !$this->app->accountManager->get($request->header->account)->hasPermission(
            PermissionType::CREATE, $this->app->unitId, $this->app->appId, self::NAME, PermissionCategory::SESSION_ELEMENT_FOREIGN
        )) {
            $response->header->status = RequestStatusCode::R_403;
            return;
        }

        $element = $this->createSessionElementFromRequest($request);

        if ($element === null) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $element);

            return;
        }

        $this->createModel($request->header->account, $element, SessionElementMapper::class, 'element', $request->getOrigin());

        if ($element->status === ClockingStatus::END) {
            /** @var \Modules\HumanResourceTimeRecording\Models\Session $session */
            $session = SessionMapper::get()
                ->with('sessionElements')
                ->where('id', $element->session->id)
                ->execute();

            $session->recalculate();
            SessionMapper::update()->execute($session);
        }

        $this->createStandardCreateResponse($request, $response, $element);
    }

    /**
     * Method to create session element from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return SessionElement Returns the created session element from the request
     *
     * @since 1.0.0
     */
    private function createSessionElementFromRequest(RequestAbstract $request) : ?SessionElement
    {
        $session = null;
        if ($request->hasData('session')) {
            /** @var Session $session */
            $session = SessionMapper::get()
                ->where('id', (int) $request->getData('session'))
                ->execute();
        } else {
            /** @var Session $session */
            $session = SessionMapper::get()
                ->where('employee', $request->getDataInt('account') ?? $request->header->account)
                ->sort('createdAt', OrderType::DESC)
                ->limit(1)
                ->execute();
        }

        // cannot create session element for none existing session
        if ($session->id === 0) {
            return null;
        }

        // a custom datetime can only be set if the user is allowed to create a session for a foreign account or if the session is a vacation
        $dt = $request->hasData('account') || $session->type === ClockingType::VACATION
            ? ($request->getDataDateTime('datetime') ?? new \DateTime('now'))
            : new \DateTime('now');

        $element          = new SessionElement($session, $dt);
        $element->status  = ClockingStatus::tryFromValue($request->getDataInt('status')) ?? ClockingStatus::END;
        $element->session = $session;

        return $element;
    }

    /**
     * Api method to update a session
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSessionUpdate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
    }

    /**
     * Api method to update a session element
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSessionElementUpdate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
    }

    /**
     * Api method to update a session
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSessionDelete(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
    }

    /**
     * Api method to update a session element
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSessionElementDelete(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
    }
}
