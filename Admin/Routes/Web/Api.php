<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\HumanResourceTimeRecording\Controller\ApiController;
use Modules\HumanResourceTimeRecording\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/humanresource/timerecording/session(\?.*$|$)' => [
        [
            'dest'       => '\Modules\HumanResourceTimeRecording\Controller\ApiController:apiSessionCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::SESSION,
            ],
        ],
    ],
    '^.*/humanresource/timerecording/element(\?.*$|$)' => [
        [
            'dest'       => '\Modules\HumanResourceTimeRecording\Controller\ApiController:apiSessionElementCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::SESSION_ELEMENT,
            ],
        ],
    ],
];
