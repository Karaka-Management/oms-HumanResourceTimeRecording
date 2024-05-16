<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\HumanResourceTimeRecording\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\HumanResourceTimeRecording\Models;

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;
use phpOMS\Localization\BaseStringL11n;

/**
 * Clocking type l11n mapper class.
 *
 * @package Modules\HumanResourceTimeRecording\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of BaseStringL11n
 * @extends DataMapperFactory<T>
 */
final class ClockingTypeL11nMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'hr_timerecording_type_l11n_id'       => ['name' => 'hr_timerecording_type_l11n_id',       'type' => 'int',    'internal' => 'id'],
        'hr_timerecording_type_l11n_title'    => ['name' => 'hr_timerecording_type_l11n_title',    'type' => 'string', 'internal' => 'content', 'autocomplete' => true],
        'hr_timerecording_type_l11n_type'     => ['name' => 'hr_timerecording_type_l11n_type',     'type' => 'int',    'internal' => 'ref'],
        'hr_timerecording_type_l11n_language' => ['name' => 'hr_timerecording_type_l11n_language', 'type' => 'string', 'internal' => 'language'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'hr_timerecording_type_l11n';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'hr_timerecording_type_l11n_id';

    /**
     * Model to use by the mapper.
     *
     * @var class-string<T>
     * @since 1.0.0
     */
    public const MODEL = BaseStringL11n::class;
}
