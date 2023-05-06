<?php
/**
 * Karaka
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

use Modules\HumanResourceTimeRecording\Models\NullSessionElement;

/**
 * @internal
 */
final class NullSessionElementTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\HumanResourceTimeRecording\Models\NullSessionElement
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\HumanResourceTimeRecording\Models\SessionElement', new NullSessionElement());
    }

    /**
     * @covers Modules\HumanResourceTimeRecording\Models\NullSessionElement
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullSessionElement(2);
        self::assertEquals(2, $null->id);
    }
}
