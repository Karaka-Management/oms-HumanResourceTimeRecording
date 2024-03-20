<?php
/**
 * Jingga
 *
 * PHP Version 8.2
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
use Modules\HumanResourceTimeRecording\Models\NullSession;
use Modules\HumanResourceTimeRecording\Models\SessionElement;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\HumanResourceTimeRecording\Models\SessionElement::class)]
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

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testDefault() : void
    {
        self::assertEquals(0, $this->element->id);
        self::assertEquals(0, $this->element->session->id);
        self::assertInstanceOf('\DateTime', $this->element->datetime);
        self::assertEquals(ClockingStatus::START, $this->element->status);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testStatusInputOutput() : void
    {
        $this->element->status = ClockingStatus::END;
        self::assertEquals(ClockingStatus::END, $this->element->status);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testSerialize() : void
    {
        $this->element->session = new NullSession(2);
        $this->element->status = ClockingStatus::END;

        $serialized = $this->element->jsonSerialize();
        unset($serialized['datetime']);
        unset($serialized['session']);

        self::assertEquals(
            [
                'id'     => 0,
                'status' => ClockingStatus::END,
            ],
            $serialized
        );
    }
}
