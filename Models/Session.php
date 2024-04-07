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

use Modules\Admin\Models\Account;
use Modules\Admin\Models\NullAccount;

/**
 * Session model
 *
 * @package Modules\HumanResourceTimeRecording\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
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
    public int $id = 0;

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
    public int $busy = 0;

    /**
     * Session type.
     *
     * @var int
     * @since 1.0.0
     */
    public int $type = ClockingType::NO_DATA;

    /**
     * Session elements.
     *
     * @var array
     * @since 1.0.0
     */
    public array $sessionElements = [];

    /**
     * Employee.
     *
     * @var Account
     * @since 1.0.0
     */
    public Account $employee;

    /**
     * Session start
     *
     * @var \DateTimeImmutable
     * @since 1.0.0
     */
    public \DateTimeImmutable $createdAt;

    /**
     * Constructor.
     *
     * @param Account $employee Account
     *
     * @since 1.0.0
     */
    public function __construct(?Account $employee = null)
    {
        $this->start     = new \DateTime('now');
        $this->employee  = $employee ?? new NullAccount();
        $this->createdAt = new \DateTimeImmutable('now');
    }

    public function recalculate() : void
    {
        \usort($this->sessionElements, [$this, 'compareSessionElementTimestamps']);

        $start = null;
        $end   = null;

        foreach ($this->sessionElements as $e) {
            if ($e->status === ClockingStatus::START
                && ($start === null || $start->getTimestamp() > $e->datetime->getTimestamp())
            ) {
                $start = $e->datetime;
            } elseif ($e->status === ClockingStatus::END
                && ($end === null || $end->getTimestamp() < $e->datetime->getTimestamp())
            ) {
                $end = $e->datetime;
            }
        }

        $this->start = $start;
        $this->end   = $end;

        $busyTime  = 0;
        $lastStart = $this->start;

        foreach ($this->sessionElements as $e) {
            if ($e->status === ClockingStatus::START) {
                $lastStart = $e->datetime;

                continue;
            }

            if ($e->status === ClockingStatus::PAUSE || $e->status === ClockingStatus::END) {
                $busyTime += $e->datetime->getTimestamp() - $lastStart->getTimestamp();
            }

            if ($e->status === ClockingStatus::CONTINUE) {
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
     * Compare session elements
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
        if (empty($this->sessionElements)) {
            return ClockingStatus::START;
        }

        \usort($this->sessionElements, [$this, 'compareSessionElementTimestamps']);

        $last = \end($this->sessionElements);

        \reset($this->sessionElements);

        return $last->status;
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
            if ($element->status === ClockingStatus::START) {
                continue;
            }

            if ($element->status === ClockingStatus::PAUSE || $element->status === ClockingStatus::END) {
                $lastBreak = $element->datetime;
            }

            if ($element->status === ClockingStatus::CONTINUE) {
                $breakTime += $element->datetime->getTimestamp() - ($lastBreak->getTimestamp() ?? 0);
            }
        }

        return $breakTime;
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
