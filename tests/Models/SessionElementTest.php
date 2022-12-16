<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\HumanResourceTimeRecording\tests\Models;

use Modules\HumanResourceTimeRecording\Models\ClockingStatus;
use Modules\HumanResourceTimeRecording\Models\NullSession;
use Modules\HumanResourceTimeRecording\Models\SessionElement;

/**
 * @internal
 */
final class SessionElementTest extends \PHPUnit\Framework\TestCase
{
    private SessionElement $element;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->element = new SessionElement();
    }

    /**
     * @covers Modules\HumanResourceTimeRecording\Models\SessionElement
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->element->getId());
        self::assertEquals(0, $this->element->session->getId());
        self::assertInstanceOf('\DateTime', $this->element->datetime);
        self::assertEquals(ClockingStatus::START, $this->element->getStatus());
    }

    /**
     * @covers Modules\HumanResourceTimeRecording\Models\SessionElement
     * @group module
     */
    public function testStatusInputOutput() : void
    {
        $this->element->setStatus(ClockingStatus::END);
        self::assertEquals(ClockingStatus::END, $this->element->getStatus());
    }

    /**
     * @covers Modules\HumanResourceTimeRecording\Models\SessionElement
     * @group module
     */
    public function testSerialize() : void
    {
        $this->element->session = new NullSession(2);
        $this->element->setStatus(ClockingStatus::END);

        $serialized = $this->element->jsonSerialize();
        unset($serialized['datetime']);
        unset($serialized['session']);

        self::assertEquals(
            [
                'id'          => 0,
                'status'      => ClockingStatus::END,
            ],
            $serialized
        );
    }
}
