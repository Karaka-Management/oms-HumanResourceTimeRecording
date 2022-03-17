<?php declare(strict_types=1);

use Modules\HumanResourceTimeRecording\Controller\TimerecordingController;
use Modules\HumanResourceTimeRecording\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^/timerecording$' => [
        [
            'dest'       => '\Modules\HumanResourceTimeRecording\Controller\TimerecordingController:viewDashboard',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => TimerecordingController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
    '^.*/timerecording/dashboard.*$' => [
        [
            'dest'       => '\Modules\HumanResourceTimeRecording\Controller\TimerecordingController:viewDashboard',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => TimerecordingController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DASHBOARD,
            ],
        ],
    ],
];
