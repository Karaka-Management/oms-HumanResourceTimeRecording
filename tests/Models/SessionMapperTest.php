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

use Modules\HumanResourceManagement\Models\NullEmployee;
use Modules\HumanResourceTimeRecording\Models\ClockingStatus;
use Modules\HumanResourceTimeRecording\Models\Session;
use Modules\HumanResourceTimeRecording\Models\SessionElement;
use Modules\HumanResourceTimeRecording\Models\SessionMapper;

/**
 * @internal
 */
final class SessionMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\HumanResourceTimeRecording\Models\SessionMapper
     * @group module
     */
    public function testCRUD() : void
    {
        $session = new Session(new NullEmployee(1));

        $dt      = new \DateTime(\date('Y-m-d', \strtotime('now')) . ' 7:55:34');
        $element = new SessionElement($session, $dt);
        $element->setStatus(ClockingStatus::START);
        $session->addSessionElement($element);

        $id = SessionMapper::create()->execute($session);
        self::assertGreaterThan(0, $session->getId());
        self::assertEquals($id, $session->getId());

        $sessionR = SessionMapper::get()->where('id', $session->getId())->execute();
        self::assertEquals($session->getType(), $sessionR->getType());

        self::assertGreaterThan(0, \count(SessionMapper::getLastSessionsFromAllEmployees()));
        self::assertNull(SessionMapper::getMostPlausibleOpenSessionForEmployee(9999));

        // @todo implement
        // self::assertGreaterThan(0, SessionMapper::getMostPlausibleOpenSessionForEmployee(1)->getId());
    }
}
