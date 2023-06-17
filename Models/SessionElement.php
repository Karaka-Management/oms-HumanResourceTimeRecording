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

/**
 * Session element model
 *
 * @package Modules\HumanResourceTimeRecording\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class SessionElement implements \JsonSerializable
{
    /**
     * Session element ID.
     *
     * @var int
     * @since 1.0.0
     */
    public int $id = 0;

    /**
     * Session element status.
     *
     * @var int
     * @since 1.0.0
     */
    public int $status = ClockingStatus::START;

    /**
     * DateTime
     *
     * @var \DateTime
     * @since 1.0.0
     */
    public \DateTime $datetime;

    /**
     * Session id this element belongs to
     *
     * @var int|Session
     * @since 1.0.0
     */
    public int|Session $session;

    /**
     * Constructor.
     *
     * @param Session        $session  Session id
     * @param null|\DateTime $datetime DateTime of the session element
     *
     * @since 1.0.0
     */
    public function __construct(Session $session = null, \DateTime $datetime = null)
    {
        $this->session  = $session ?? new NullSession();
        $this->datetime = $datetime ?? new \DateTime('now');
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
     * Get the session element status
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getStatus() : int
    {
        return $this->status;
    }

    /**
     * Set the session element status
     *
     * @param int $status Session element status
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setStatus(int $status) : void
    {
        $this->status = $status;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'             => $this->id,
            'status'         => $this->status,
            'datetime'       => $this->datetime,
            'session'        => $this->session,
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
