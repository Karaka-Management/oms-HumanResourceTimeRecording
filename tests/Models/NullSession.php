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

use Modules\HumanResourceTimeRecording\Models\NullSession;

/**
 * @internal
 */
final class Null extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\HumanResourceTimeRecording\Models\NullSession
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\HumanResourceTimeRecording\Models\Session', new NullSession());
    }

    /**
     * @covers Modules\HumanResourceTimeRecording\Models\NullSession
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullSession(2);
        self::assertEquals(2, $null->getId());
    }
}
