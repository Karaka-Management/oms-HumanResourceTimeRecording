<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   Modules\HumanResourceTimeRecording\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\HumanResourceTimeRecording\Models;

use phpOMS\Stdlib\Base\Enum;

/**
 * ClockingStatus enum.
 *
 * @package Modules\HumanResourceTimeRecording\Models
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
abstract class ClockingStatus extends Enum
{
    public const START = 1;

    public const PAUSE = 2;

    public const CONTINUE = 3;

    public const END = 4;
}
