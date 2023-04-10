<?php
/**
 * Karaka
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

use Modules\HumanResourceManagement\Models\EmployeeMapper;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;
use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\Stdlib\Base\SmartDateTime;

/**
 * Mapper class.
 *
 * @package Modules\HumanResourceTimeRecording\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of Session
 * @extends DataMapperFactory<T>
 */
final class SessionMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'hr_timerecording_session_id'       => ['name' => 'hr_timerecording_session_id',       'type' => 'int',      'internal' => 'id'],
        'hr_timerecording_session_type'     => ['name' => 'hr_timerecording_session_type',     'type' => 'int',      'internal' => 'type'],
        'hr_timerecording_session_start'    => ['name' => 'hr_timerecording_session_start',    'type' => 'DateTime', 'internal' => 'start'],
        'hr_timerecording_session_end'      => ['name' => 'hr_timerecording_session_end',      'type' => 'DateTime', 'internal' => 'end'],
        'hr_timerecording_session_busy'     => ['name' => 'hr_timerecording_session_busy',     'type' => 'int',      'internal' => 'busy'],
        'hr_timerecording_session_employee' => ['name' => 'hr_timerecording_session_employee', 'type' => 'int',      'internal' => 'employee'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'sessionElements' => [
            'mapper'   => SessionElementMapper::class,
            'table'    => 'hr_timerecording_session_element',
            'self'     => 'hr_timerecording_session_element_session',
            'external' => null,
        ],
    ];

    /**
     * Belongs to.
     *
     * @var array<string, array{mapper:class-string, external:string, column?:string, by?:string}>
     * @since 1.0.0
     */
    public const BELONGS_TO = [
        'employee' => [
            'mapper'   => EmployeeMapper::class,
            'external' => 'hr_timerecording_session_employee',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'hr_timerecording_session';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'hr_timerecording_session_id';

    /**
     * Created at column
     *
     * @var string
     * @since 1.0.0
     */
    public const CREATED_AT = 'hr_timerecording_session_start';

    /**
     * Get last sessions from all employees
     *
     * @return Session[]
     *
     * @since 1.0.0
     */
    public static function getLastSessionsFromAllEmployees() : array
    {
        $join = new Builder(self::$db);
        $join->select(self::TABLE . '.hr_timerecording_session_employee')
            ->selectAs('MAX(hr_timerecording_session_start)', 'maxDate')
            ->from(self::TABLE)
            ->groupBy(self::TABLE . '.hr_timerecording_session_employee');

        $query = self::getQuery();
        $query->innerJoin($join, 'tm')
            ->on(self::TABLE . '_d1.hr_timerecording_session_employee', '=', 'tm.hr_timerecording_session_employee')
            ->andOn(self::TABLE . '_d1.hr_timerecording_session_start', '=', 'tm.maxDate');

        return self::getAll()->execute($query);
    }

    /**
     * Get the most plausible open session for an employee.
     *
     * This searches for an open session that could be ongoing. This is required to automatically select
     * the current session for breaks, work continuation and ending work sessions without manually selecting
     * the current session.
     *
     * @param int $employee Employee id
     *
     * @return null|Session
     *
     * @since 1.0.0
     */
    public static function getMostPlausibleOpenSessionForEmployee(int $employee) : ?Session
    {
        $dt = new SmartDateTime('now');
        $dt->smartModify(0, 0, -32);

        $query = self::getQuery();
        $query->where(self::TABLE . '_d1.hr_timerecording_session_employee', '=', $employee)
            ->andWhere(self::TABLE . '_d1.hr_timerecording_session_start', '>', $dt)
            ->orderBy(self::TABLE . '_d1.hr_timerecording_session_start', 'DESC')
            ->limit(1);

        /** @var Session[] $sessions */
        $sessions = self::getAll()->execute($query);

        if (empty($sessions)) {
            return null;
        }

        if (\end($sessions)->end === null) {
            return \end($sessions);
        }

        return null;
    }
}
