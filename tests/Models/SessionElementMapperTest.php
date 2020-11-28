<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\HumanResourceTimeRecording\tests\Models;

use Modules\HumanResourceManagement\Models\NullEmployee;
use Modules\HumanResourceTimeRecording\Models\Session;
use Modules\HumanResourceTimeRecording\Models\SessionElement;
use Modules\HumanResourceTimeRecording\Models\SessionElementMapper;

/**
 * @internal
 */
class SessionElementMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\HumanResourceTimeRecording\Models\SessionElementMapper
     * @group module
     */
    public function testCRUD() : void
    {
        $element = new SessionElement(new Session(new NullEmployee(1)), new \DateTime('now'));

        $id = SessionElementMapper::create($element);
        self::assertGreaterThan(0, $element->getId());
        self::assertEquals($id, $element->getId());

        $elementR = SessionElementMapper::get($element->getId());
        self::assertEquals($element->getDatetime()->format('Y-m-d'), $elementR->getDatetime()->format('Y-m-d'));
        self::assertEquals($element->getStatus(), $elementR->getStatus());
        self::assertEquals($element->session->getEmployee()->getId(), $elementR->session->getEmployee()->getId());
    }
}
