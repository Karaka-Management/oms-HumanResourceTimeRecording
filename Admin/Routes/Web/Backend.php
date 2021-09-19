<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

use Modules\HumanResourceTimeRecording\Controller\BackendController;
use Modules\HumanResourceTimeRecording\Models\PermissionState;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/humanresource/timerecording/dashboard.*$' => [
        [
            'dest'       => '\Modules\HumanResourceTimeRecording\Controller\BackendController:viewDashboard',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::MODULE_NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::DASHBOARD,
            ],
        ],
    ],
    '^.*/private/timerecording/dashboard.*$' => [
        [
            'dest'       => '\Modules\HumanResourceTimeRecording\Controller\BackendController:viewPrivateDashboard',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::MODULE_NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::PRIVATE_DASHBOARD,
            ],
        ],
    ],
    '^.*/private/timerecording/session.*$' => [
        [
            'dest'       => '\Modules\HumanResourceTimeRecording\Controller\BackendController:viewPrivateSession',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::MODULE_NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::PRIVATE_DASHBOARD,
            ],
        ],
    ],
];
