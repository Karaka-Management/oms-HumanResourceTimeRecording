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

/**
 * Clocking type mapper class.
 *
 * @package Modules\HumanResourceTimeRecording\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of ClockingType
 * @extends DataMapperFactory<T>
 */
final class ClockingTypeMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'hr_timerecording_type_id'            => ['name' => 'hr_timerecording_type_id',        'type' => 'int',    'internal' => 'id'],
        'hr_timerecording_type_name'          => ['name' => 'hr_timerecording_type_name',      'type' => 'string', 'internal' => 'name'],
        'hr_timerecording_type_custom_future' => ['name' => 'hr_timerecording_type_custom_future',      'type' => 'bool', 'internal' => 'customFutureTimeAllowed'],
        'hr_timerecording_type_custom_past'   => ['name' => 'hr_timerecording_type_custom_past',      'type' => 'bool', 'internal' => 'customPastTimeAllowed'],
        'hr_timerecording_type_correction'    => ['name' => 'hr_timerecording_type_correction',      'type' => 'bool', 'internal' => 'correctionAllowed'],
        'hr_timerecording_type_work'          => ['name' => 'hr_timerecording_type_work',      'type' => 'bool', 'internal' => 'isWork'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'l11n' => [
            'mapper'   => ClockingTypeL11nMapper::class,
            'table'    => 'hr_timerecording_type_l11n',
            'self'     => 'hr_timerecording_type_l11n_type',
            'column'   => 'content',
            'external' => null,
        ],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var class-string<T>
     * @since 1.0.0
     */
    public const MODEL = ClockingType::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'hr_timerecording_type';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'hr_timerecording_type_id';
}
