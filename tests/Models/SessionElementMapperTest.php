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

use Modules\Admin\Models\NullAccount;
use Modules\HumanResourceTimeRecording\Models\Session;
use Modules\HumanResourceTimeRecording\Models\SessionElement;
use Modules\HumanResourceTimeRecording\Models\SessionElementMapper;

/**
 * @internal
 */
final class SessionElementMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Modules\HumanResourceTimeRecording\Models\SessionElementMapper
     * @group module
     */
    public function testCRUD() : void
    {
        $element = new SessionElement(new Session(new NullAccount(1)), new \DateTime('now'));

        $id = SessionElementMapper::create()->execute($element);
        self::assertGreaterThan(0, $element->id);
        self::assertEquals($id, $element->id);

        $elementR = SessionElementMapper::get()->with('session')->with('employee')->where('id', $element->id)->execute();
        self::assertEquals($element->datetime->format('Y-m-d'), $elementR->datetime->format('Y-m-d'));
        self::assertEquals($element->status, $elementR->status);
        self::assertEquals($element->session->employee->id, $elementR->session->employee->id);
    }
}
