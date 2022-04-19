<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\HumanResourceTimeRecording
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\HumanResourceTimeRecording\Controller;

use Modules\HumanResourceTimeRecording\Models\SessionMapper;
use phpOMS\Contract\RenderableInterface;
use phpOMS\DataStorage\Database\Query\OrderType;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Views\View;

/**
 * TimeRecording controller class.
 *
 * @package Modules\HumanResourceTimeRecording
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
final class TimerecordingController extends Controller
{
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
    public function viewDashboard(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/HumanResourceTimeRecording/Theme/Timeterminal/overview');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1006301001, $request, $response));

        $list = SessionMapper::getAll()->sort('id', OrderType::DESC)->limit(50)->execute();
        $view->addData('sessions', $list);

        return $view;
    }
}
