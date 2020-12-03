<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\HumanResourceTimeRecording\tests\Models;

use Modules\HumanResourceTimeRecording\Models\ClockingStatus;
use Modules\HumanResourceTimeRecording\Models\SessionElement;

/**
 * @internal
 */
class SessionElementTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\HumanResourceTimeRecording\Models\SessionElement
     * @group module
     */
    public function testDefault() : void
    {
        $element = new SessionElement();

        self::assertEquals(0, $element->getId());
        self::assertEquals(0, $element->session->getId());
        self::assertEquals((new \DateTime('now'))->format('Y-m-d'), $element->getDatetime()->format('Y-m-d'));
        self::assertEquals(ClockingStatus::START, $element->getStatus());
    }

    /**
     * @covers Modules\HumanResourceTimeRecording\Models\SessionElement
     * @group module
     */
    public function testSetGet() : void
    {
        $element = new SessionElement();

        $element->setStatus(ClockingStatus::END);
        self::assertEquals(ClockingStatus::END, $element->getStatus());
    }
}
