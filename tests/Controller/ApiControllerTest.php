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

namespace Modules\HumanResourceTimeRecording\tests\Controller;

use Model\CoreSettings;
use Modules\Admin\Models\AccountPermission;
use phpOMS\Account\Account;
use phpOMS\Account\AccountManager;
use phpOMS\Account\PermissionType;
use phpOMS\Application\ApplicationAbstract;
use phpOMS\DataStorage\Session\HttpSession;
use phpOMS\Dispatcher\Dispatcher;
use phpOMS\Event\EventManager;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Module\ModuleAbstract;
use phpOMS\Module\ModuleManager;
use phpOMS\Router\WebRouter;
use phpOMS\System\MimeType;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\TestUtils;
use Modules\HumanResourceTimeRecording\Models\ClockingStatus;

/**
 * @testdox Modules\HumanResourceTimeRecording\tests\Controller\ApiControllerTest: HumanResourceTimeRecording api controller
 *
 * @internal
 */
final class ApiControllerTest extends \PHPUnit\Framework\TestCase
{
    protected ApplicationAbstract $app;

    /**
     * @var \Modules\HumanResourceTimeRecording\Controller\ApiController
     */
    protected ModuleAbstract $module;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->app = new class() extends ApplicationAbstract
        {
            protected string $appName = 'Api';
        };

        $this->app->dbPool         = $GLOBALS['dbpool'];
        $this->app->orgId          = 1;
        $this->app->accountManager = new AccountManager($GLOBALS['session']);
        $this->app->appSettings    = new CoreSettings();
        $this->app->moduleManager  = new ModuleManager($this->app, __DIR__ . '/../../../../Modules/');
        $this->app->dispatcher     = new Dispatcher($this->app);
        $this->app->eventManager   = new EventManager($this->app->dispatcher);
        $this->app->eventManager->importFromFile(__DIR__ . '/../../../../Web/Api/Hooks.php');
        $this->app->sessionManager = new HttpSession(36000);

        $account = new Account();
        TestUtils::setMember($account, 'id', 1);

        $permission = new AccountPermission();
        $permission->setUnit(1);
        $permission->setApp('backend');
        $permission->setPermission(
            PermissionType::READ
            | PermissionType::CREATE
            | PermissionType::MODIFY
            | PermissionType::DELETE
            | PermissionType::PERMISSION
        );

        $account->addPermission($permission);

        $this->app->accountManager->add($account);
        $this->app->router = new WebRouter();

        $this->module = $this->app->moduleManager->get('HumanResourceTimeRecording');

        TestUtils::setMember($this->module, 'app', $this->app);
    }

    /**
     * @covers Modules\HumanResourceTimeRecording\Controller\ApiController
     * @group module
     */
    public function testApiSessionCR() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;

        $this->module->apiSessionCreate($request, $response);
        self::assertGreaterThan(0, $sId = $response->get('')['response']->getId());

        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('session', (string) $sId);
        $request->setData('status', ClockingStatus::END);

        $this->module->apiSessionElementCreate($request, $response);
        self::assertGreaterThan(0, $response->get('')['response']->getId());
    }

    /**
     * @covers Modules\HumanResourceTimeRecording\Controller\ApiController
     * @group module
     */
    public function testApiSessionCreateInvalidPermission() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 9999;
        $request->setData('account', 2);

        $this->module->apiSessionCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_403, $response->header->status);
    }

    /**
     * @covers Modules\HumanResourceTimeRecording\Controller\ApiController
     * @group module
     */
    public function testApiSessionCreateInvalidDataEmployee() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('account', 9999);

        $this->module->apiSessionCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    /**
     * @covers Modules\HumanResourceTimeRecording\Controller\ApiController
     * @group module
     */
    public function testApiSessionElementCreateInvalidData() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('invalid', 1);

        $this->module->apiSessionElementCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    /**
     * @covers Modules\HumanResourceTimeRecording\Controller\ApiController
     * @group module
     */
    public function testApiSessionElementCreateInvalidSessionId() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('session', 99999);
        $request->setData('status', ClockingStatus::CONTINUE);

        $this->module->apiSessionElementCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    /**
     * @covers Modules\HumanResourceTimeRecording\Controller\ApiController
     * @group module
     */
    public function testApiSessionElementCreateInvalidPermission() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 9999;
        $request->setData('session', 1);
        $request->setData('account', 1);

        $this->module->apiSessionElementCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_403, $response->header->status);
    }
}