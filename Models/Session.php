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

use Modules\HumanResourceManagement\Models\Employee;
use Modules\HumanResourceManagement\Models\NullEmployee;

/**
 * Session model
 *
 * @package Modules\HumanResourceTimeRecording\Models
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
class Session implements \JsonSerializable
{
    /**
     * Session ID.
     *
     * @var int
     * @since 1.0.0
     */
    protected int $id = 0;

    /**
     * Session start
     *
     * @var \DateTime
     * @since 1.0.0
     */
    public \DateTime $start;

    /**
     * Session end
     *
     * @var null|\DateTime
     * @since 1.0.0
     */
    public ?\DateTime $end = null;

    /**
     * Busy time.
     *
     * @var int
     * @since 1.0.0
     */
    private int $busy = 0;

    /**
     * Session type.
     *
     * @var int
     * @since 1.0.0
     */
    private int $type = ClockingType::OFFICE;

    /**
     * Session elements.
     *
     * @var array
     * @since 1.0.0
     */
    private array $sessionElements = [];

    /**
     * Employee.
     *
     * @var Employee
     * @since 1.0.0
     */
    public Employee $employee;

    /**
     * Constructor.
     *
     * @param Employee $employee Employee
     *
     * @since 1.0.0
     */
    public function __construct(Employee $employee = null)
    {
        $this->start    = new \DateTime('now');
        $this->employee = $employee ?? new NullEmployee();
    }

    /**
     * Get id.
     *
     * @return int Account id
     *
     * @since 1.0.0
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get busy time.
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getBusy() : int
    {
        return $this->busy;
    }

    /**
     * Add a session element to the session
     *
     * @param SessionElement $element Session element
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function addSessionElement(SessionElement $element) : void
    {
        if ($element->getStatus() === ClockingStatus::START) {
            foreach ($this->sessionElements as $e) {
                if ($e->getStatus() === ClockingStatus::START) {
                    return;
                }
            }

            $this->start = $element->datetime;
        }

        if ($element->getStatus() === ClockingStatus::END) {
            if ($this->end !== null) {
                return;
            }

            $this->end = $element->datetime;
        }

        $this->sessionElements[] = $element;

        \usort($this->sessionElements, [$this, 'compareSessionElementTimestamps']);

        $busyTime  = 0;
        $lastStart = $this->start;

        foreach ($this->sessionElements as $e) {
            if ($e->getStatus() === ClockingStatus::START) {
                continue;
            }

            if ($e->getStatus() === ClockingStatus::PAUSE || $e->getStatus() === ClockingStatus::END) {
                $busyTime += $e->datetime->getTimestamp() - $lastStart->getTimestamp();
            }

            if ($e->getStatus() === ClockingStatus::CONTINUE) {
                $lastStart = $e->datetime;
            }
        }

        $this->busy = $busyTime;
    }

    /**
     * Get all session elements
     *
     * @return SessionElement[]
     *
     * @since 1.0.0
     */
    public function getSessionElements() : array
    {
        return $this->sessionElements;
    }

    /**
     * Compare session selements
     *
     * @param SessionElement $a First session element
     * @param SessionElement $b Second session element
     *
     * @return int
     *
     * @since 1.0.0
     */
    private function compareSessionElementTimestamps(SessionElement $a, SessionElement $b) : int
    {
        return $a->datetime->getTimestamp() <=> $b->datetime->getTimestamp();
    }

    /**
     * Get the status of the last session element
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getStatus() : int
    {
        if (\count($this->sessionElements) === 0) {
            return ClockingStatus::START;
        }

        \usort($this->sessionElements, [$this, 'compareSessionElementTimestamps']);

        $last = \end($this->sessionElements);

        \reset($this->sessionElements);

        return $last->getStatus();
    }

    /**
     * Get the total break time of a session
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getBreak() : int
    {
        \usort($this->sessionElements, [$this, 'compareSessionElementTimestamps']);

        $breakTime = 0;
        $lastBreak = $this->start;

        foreach ($this->sessionElements as $element) {
            if ($element->getStatus() === ClockingStatus::START) {
                continue;
            }

            if ($element->getStatus() === ClockingStatus::PAUSE || $element->getStatus() === ClockingStatus::END) {
                $lastBreak = $element->datetime;
            }

            if ($element->getStatus() === ClockingStatus::CONTINUE) {
                $breakTime += $element->datetime->getTimestamp() - ($lastBreak->getTimestamp() ?? 0);
            }
        }

        return $breakTime;
    }

    /**
     * Get the session type
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getType() : int
    {
        return $this->type;
    }

    /**
     * Set the session type
     *
     * @param int $type Session type
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setType(int $type) : void
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'       => $this->id,
            'start'    => $this->start,
            'end'      => $this->end,
            'busy'     => $this->busy,
            'type'     => $this->type,
            'employee' => $this->employee,
            'elements' => $this->sessionElements,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() : mixed
    {
        return $this->toArray();
    }
}
