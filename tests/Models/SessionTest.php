<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\HumanResourceTimeRecording\tests\Models;

use Modules\HumanResourceTimeRecording\Models\ClockingStatus;
use Modules\HumanResourceTimeRecording\Models\ClockingType;
use Modules\HumanResourceTimeRecording\Models\Session;
use Modules\HumanResourceTimeRecording\Models\SessionElement;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\HumanResourceTimeRecording\Models\Session::class)]
final class SessionTest extends \PHPUnit\Framework\TestCase
{
    private Session $session;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->session = new Session();
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testDefault() : void
    {
        self::assertEquals(0, $this->session->id);
        self::assertEquals(0, $this->session->getBusy());
        self::assertEquals(0, $this->session->getBreak());
        self::assertEquals([], $this->session->getSessionElements());
        self::assertEquals(ClockingType::OFFICE, $this->session->type);
        self::assertEquals(ClockingStatus::START, $this->session->getStatus());
        self::assertEquals((new \DateTime('now'))->format('Y-m-d'), $this->session->start->format('Y-m-d'));
        self::assertNull($this->session->end);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testStatusInputOutput() : void
    {
        $element         = new SessionElement(null, new \DateTime('2021-10-05'));
        $element->status = ClockingStatus::START;
        $this->session->addSessionElement($element);

        $element         = new SessionElement(null, new \DateTime('2021-10-06'));
        $element->status = ClockingStatus::PAUSE;
        $this->session->addSessionElement($element);

        $element         = new SessionElement(null, new \DateTime('2021-10-07'));
        $element->status = ClockingStatus::CONTINUE;
        $this->session->addSessionElement($element);

        self::assertEquals(ClockingStatus::CONTINUE, $this->session->getStatus());
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testBusyBreakInputOutput() : void
    {
        $element         = new SessionElement(null, new \DateTime('2021-10-05 02:00:00'));
        $element->status = ClockingStatus::START;
        $this->session->addSessionElement($element);

        // this is ignored because the session is already started
        $element         = new SessionElement(null, new \DateTime('2021-10-05 03:00:00'));
        $element->status = ClockingStatus::START;
        $this->session->addSessionElement($element);

        $element         = new SessionElement(null, new \DateTime('2021-10-05 04:00:00'));
        $element->status = ClockingStatus::PAUSE;
        $this->session->addSessionElement($element);

        $element         = new SessionElement(null, new \DateTime('2021-10-05 04:30:00'));
        $element->status = ClockingStatus::CONTINUE;
        $this->session->addSessionElement($element);

        $element         = new SessionElement(null, new \DateTime('2021-10-05 07:00:00'));
        $element->status = ClockingStatus::PAUSE;
        $this->session->addSessionElement($element);

        $element         = new SessionElement(null, new \DateTime('2021-10-05 08:30:00'));
        $element->status = ClockingStatus::CONTINUE;
        $this->session->addSessionElement($element);

        $element         = new SessionElement(null, new \DateTime('2021-10-05 11:00:00'));
        $element->status = ClockingStatus::END;
        $this->session->addSessionElement($element);

        // this is ignored because the session is already stopped
        $element         = new SessionElement(null, new \DateTime('2021-10-05 11:30:00'));
        $element->status = ClockingStatus::END;
        $this->session->addSessionElement($element);

        self::assertEquals(2 * 60 * 60, $this->session->getBreak());
        self::assertEquals(7 * 60 * 60, $this->session->getBusy());
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testSessionElementInputOutput() : void
    {
        $element = new SessionElement(null, new \DateTime('now'));
        $this->session->addSessionElement($element);
        self::assertCount(1, $this->session->getSessionElements());
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testSerialize() : void
    {
        $this->session->type = ClockingType::VACATION;

        $serialized = $this->session->jsonSerialize();
        unset($serialized['start']);
        unset($serialized['employee']);

        self::assertEquals(
            [
                'id'       => 0,
                'end'      => null,
                'busy'     => 0,
                'type'     => ClockingType::VACATION,
                'elements' => [],
            ],
            $serialized
        );
    }
}
