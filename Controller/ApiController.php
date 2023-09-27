<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\HumanResourceTimeRecording
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\HumanResourceTimeRecording\Controller;

use Modules\HumanResourceManagement\Models\EmployeeMapper;
use Modules\HumanResourceTimeRecording\Models\ClockingStatus;
use Modules\HumanResourceTimeRecording\Models\ClockingType;
use Modules\HumanResourceTimeRecording\Models\PermissionCategory;
use Modules\HumanResourceTimeRecording\Models\Session;
use Modules\HumanResourceTimeRecording\Models\SessionElement;
use Modules\HumanResourceTimeRecording\Models\SessionElementMapper;
use Modules\HumanResourceTimeRecording\Models\SessionMapper;
use phpOMS\Account\PermissionType;
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
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSessionCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!$this->app->accountManager->get($request->header->account)->hasPermission(
            PermissionType::CREATE, $this->app->unitId, $this->app->appId, self::NAME, PermissionCategory::SESSION_FOREIGN
        )) {
            $response->header->status = RequestStatusCode::R_403;
            $this->createInvalidCreateResponse($request, $response, []);

            return;
        }

        $session = $this->createSessionFromRequest($request);

        if ($session === null) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $session);

            return;
        }

        $this->createModel($request->header->account, $session, SessionMapper::class, 'session', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $session);
    }

    /**
     * Method to create a session from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return null|Session
     *
     * @since 1.0.0
     */
    private function createSessionFromRequest(RequestAbstract $request) : ?Session
    {
        $account = $request->getDataInt('account') ?? $request->header->account;

        $employee = EmployeeMapper::get()
            ->with('profile')
            ->with('profile/account')
            ->where('profile/account', $account)
            ->limit(1)
            ->execute();

        $type   = $request->getDataInt('type') ?? ClockingType::OFFICE;
        $status = $request->getDataInt('status') ?? ClockingStatus::START;

        if ($employee->id === 0) {
            return null;
        }

        $session = new Session($employee);
        $session->setType(ClockingType::isValidValue($type) ? $type : ClockingType::OFFICE);

        if (ClockingStatus::isValidValue($status)) {
            // a custom datetime can only be set if the user is allowed to create a session for a foreign account or if the session is a vacation
            $dt = $request->hasData('account') || $type === ClockingType::VACATION
                ? ($request->getDataDateTime('datetime') ?? new \DateTime('now'))
                : new \DateTime('now');

            $element = new SessionElement($session, $dt);
            $element->setStatus($status);

            $session->addSessionElement($element);
        }

        return $session;
    }

    /**
     * Api method to create a session element
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSessionElementCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (($request->getDataInt('status') ?? -1) === ClockingStatus::START) {
            $this->apiSessionCreate($request, $response);

            return;
        }

        if (!empty($val = $this->validateSessionElementCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        if ($request->hasData('account') && ((int) $request->getData('account')) !== $request->header->account && !$this->app->accountManager->get($request->header->account)->hasPermission(
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

        if ($element->getStatus() === ClockingStatus::END) {
            /** @var \Modules\HumanResourceTimeRecording\Models\Session $session */
            $session = SessionMapper::get()->where('id', (int) $request->getData('session'))->execute();

            $session->addSessionElement($element);
            SessionMapper::update()->execute($session);
        }

        $this->createModel($request->header->account, $element, SessionElementMapper::class, 'element', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $element);
    }

    /**
     * Validate session element create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool> Returns the validation array of the request
     *
     * @since 1.0.0
     */
    private function validateSessionElementCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['session'] = !$request->hasData('session') || !\is_numeric($request->getData('session')))) {
            return $val;
        }

        return [];
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
        $account = $request->getDataInt('account') ?? $request->header->account;

        /** @var Session $session */
        $session = SessionMapper::get()->where('id', (int) $request->getData('session'))->execute();

        // cannot create session element for none existing session
        if ($session->id === 0) {
            return null;
        }

        $status = $request->getDataInt('status') ?? -1;

        // a custom datetime can only be set if the user is allowed to create a session for a foreign account or if the session is a vacation
        $dt = $request->hasData('account') || $session->getType() === ClockingType::VACATION
            ? ($request->getDataDateTime('datetime') ?? new \DateTime('now'))
            : new \DateTime('now');

        $element = new SessionElement($session, $dt);
        $element->setStatus(ClockingStatus::isValidValue($status) ? $status : ClockingStatus::END);
        $element->session = $session;

        return $element;
    }

    /**
     * Api method to update a session
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSessionUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
    }

    /**
     * Api method to update a session element
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSessionElementUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
    }

    /**
     * Api method to update a session
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSessionDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
    }

    /**
     * Api method to update a session element
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiSessionElementDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
    }
}
