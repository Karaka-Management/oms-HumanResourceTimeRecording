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
use Modules\HumanResourceTimeRecording\Models\ClockingStatus;
use Modules\HumanResourceTimeRecording\Models\Session;
use Modules\HumanResourceTimeRecording\Models\SessionElement;
use Modules\HumanResourceTimeRecording\Models\SessionMapper;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\HumanResourceTimeRecording\Models\SessionMapper::class)]
final class SessionMapperTest extends \PHPUnit\Framework\TestCase
{
    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testCRUD() : void
    {
        $session = new Session(new NullAccount(1));

        $dt              = new \DateTime(\date('Y-m-d', \strtotime('now')) . ' 7:55:34');
        $element         = new SessionElement($session, $dt);
        $element->status = ClockingStatus::START;
        $session->addSessionElement($element);

        $id = SessionMapper::create()->execute($session);
        self::assertGreaterThan(0, $session->id);
        self::assertEquals($id, $session->id);

        $sessionR = SessionMapper::get()->where('id', $session->id)->execute();
        self::assertEquals($session->type, $sessionR->type);

        self::assertGreaterThan(0, \count(SessionMapper::getLastSessionsFromAllEmployees()));
        self::assertNull(SessionMapper::getMostPlausibleOpenSessionForEmployee(9999));

        // @todo implement
        // self::assertGreaterThan(0, SessionMapper::getMostPlausibleOpenSessionForEmployee(1)->id);
    }
}
