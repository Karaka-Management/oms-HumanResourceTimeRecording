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
 * SessionElement mapper class.
 *
 * @package Modules\HumanResourceTimeRecording\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of SessionElement
 * @extends DataMapperFactory<T>
 */
final class SessionElementMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'hr_timerecording_session_element_id'        => ['name' => 'hr_timerecording_session_element_id',      'type' => 'int',      'internal' => 'id'],
        'hr_timerecording_session_element_status'    => ['name' => 'hr_timerecording_session_element_status',  'type' => 'int',      'internal' => 'status'],
        'hr_timerecording_session_element_dt'        => ['name' => 'hr_timerecording_session_element_dt',      'type' => 'DateTime', 'internal' => 'datetime'],
        'hr_timerecording_session_element_session'   => ['name' => 'hr_timerecording_session_element_session', 'type' => 'int',      'internal' => 'session'],
        'hr_timerecording_session_element_createdat' => ['name' => 'hr_timerecording_session_element_createdat',      'type' => 'DateTimeImmutable', 'internal' => 'createdAt'],
    ];

    /**
     * Belongs to.
     *
     * @var array<string, array{mapper:class-string, external:string, column?:string, by?:string}>
     * @since 1.0.0
     */
    public const BELONGS_TO = [
        'session' => [
            'mapper'   => SessionMapper::class,
            'external' => 'hr_timerecording_session_element_session',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'hr_timerecording_session_element';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'hr_timerecording_session_element_id';
}
