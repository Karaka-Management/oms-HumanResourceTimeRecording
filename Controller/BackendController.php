<?php
/**
 * Karaka
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

use Modules\Dashboard\Models\DashboardElementInterface;
use Modules\HumanResourceManagement\Models\EmployeeMapper;
use Modules\HumanResourceTimeRecording\Models\NullSession;
use Modules\HumanResourceTimeRecording\Models\SessionMapper;
use phpOMS\Contract\RenderableInterface;
use phpOMS\DataStorage\Database\Query\OrderType;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Stdlib\Base\SmartDateTime;
use phpOMS\Views\View;

/**
 * TimeRecording controller class.
 *
 * @package Modules\HumanResourceTimeRecording
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 * @codeCoverageIgnore
 */
final class BackendController extends Controller implements DashboardElementInterface
{
    /**
     * {@inheritdoc}
     */
    public function viewDashboard(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/HumanResourceTimeRecording/Theme/Backend/dashboard');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1006301001, $request, $response);

        /** @var \Modules\HumanResourceTimeRecording\Models\Session[] $list */
        $list                   = SessionMapper::getLastSessionsFromAllEmployees();
        $view->data['sessions'] = $list;

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewPrivateDashboard(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/HumanResourceTimeRecording/Theme/Backend/private-dashboard');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1006303001, $request, $response);

        /** @var \Modules\HumanResourceManagement\Models\Employee $employee */
        $employee = EmployeeMapper::get()
            ->with('profile')
            ->with('profile/account')
            ->where('profile/account', $request->header->account)
            ->execute();

        /** @var \Modules\HumanResourceTimeRecording\Models\Session $lastOpenSession */
        $lastOpenSession = SessionMapper::getMostPlausibleOpenSessionForEmployee($employee->id);

        $start = new SmartDateTime('now');
        $start = $start->getEndOfDay();
        $limit = $start->getEndOfMonth();
        $limit->smartModify(0, -2, 0);

        $list = SessionMapper::getAll()
            ->where('employee', $employee->id)
            ->where('createdAt', $start->format('Y-m-d H:i:s'), '<=')
            ->sort('id', OrderType::DESC)
            ->execute();

        $view->data['sessions']    = $list;
        $view->data['lastSession'] = $lastOpenSession;
        $view->data['date']        = $limit;

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewPrivateSession(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/HumanResourceTimeRecording/Theme/Backend/private-session');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1006303001, $request, $response);

        /** @var \Modules\HumanResourceTimeRecording\Models\Session $session */
        $session = SessionMapper::get()->where('id', (int) $request->getData('id'))->execute();

        /** @var \Modules\HumanResourceManagement\Models\Employee $employee */
        $employee = EmployeeMapper::get()
            ->with('profile')
            ->with('profile/account')
            ->where('profile/account', $request->header->account)
            ->execute();

        if ($session->employee->id !== $employee->id) {
            $view->data['session'] = new NullSession();
        } else {
            $view->data['session'] = $session;
        }

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewHRStats(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/HumanResourceTimeRecording/Theme/Backend/hr-stats');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1006301001, $request, $response);

        /** @var \Modules\HumanResourceTimeRecording\Models\Session[] $list */
        $list                   = SessionMapper::getLastSessionsFromAllEmployees();
        $view->data['sessions'] = $list;

        return $view;
    }
}
