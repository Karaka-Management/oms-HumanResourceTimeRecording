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
 * ClockingType enum.
 *
 * @package Modules\HumanResourceTimeRecording\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
abstract class ClockingType extends Enum
{
    public const OFFICE = 1;

    public const HOME = 2;

    public const REMOTE = 3;

    public const VACATION = 4;

    public const SICK = 5;

    public const ON_THE_MOVE = 6;

    public const PAID_LEAVE = 7;

    public const UNPAID_LEAVE = 8;

    public const MATERNITY_LEAVE = 9;

    public const PARENTAL_LEAVE = 10;

    public const DR_VISIT = 11;

    public const EDUCATION = 12;

    public const TRAINING = 13;

    public const HOLIDAY = 14;

    public const NO_DATA = -1;
}
