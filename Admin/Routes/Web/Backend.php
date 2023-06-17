<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\HumanResourceTimeRecording\Controller\BackendController;
use Modules\HumanResourceTimeRecording\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/humanresource/timerecording/dashboard.*$' => [
        [
            'dest'       => '\Modules\HumanResourceTimeRecording\Controller\BackendController:viewDashboard',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^.*/private/timerecording/dashboard.*$' => [
        [
            'dest'       => '\Modules\HumanResourceTimeRecording\Controller\BackendController:viewPrivateDashboard',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::PRIVATE_DASHBOARD,
            ],
        ],
    ],
    '^.*/private/timerecording/session.*$' => [
        [
            'dest'       => '\Modules\HumanResourceTimeRecording\Controller\BackendController:viewPrivateSession',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::PRIVATE_DASHBOARD,
            ],
        ],
    ],
];
