<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\HumanResourceTimeRecording\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\HumanResourceTimeRecording\Models;

use phpOMS\Stdlib\Base\Enum;

/**
 * Permission category enum.
 *
 * @package Modules\HumanResourceTimeRecording\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
abstract class PermissionCategory extends Enum
{
    public const DASHBOARD = 1;

    public const PRIVATE_DASHBOARD = 2;

    public const SESSION = 3;

    public const SESSION_FOREIGN = 4;

    public const SESSION_ELEMENT = 5;

    public const SESSION_ELEMENT_FOREIGN = 6;
}
