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

namespace Modules\HumanResourceTimeRecording\tests\Controller;

use Model\CoreSettings;
use Modules\Admin\Models\AccountMapper;
use Modules\Admin\Models\AccountPermission;
use Modules\Admin\Models\NullAccount;
use Modules\HumanResourceTimeRecording\Models\ClockingStatus;
use phpOMS\Account\Account;
use phpOMS\Account\AccountManager;
use phpOMS\Account\PermissionType;
use phpOMS\Application\ApplicationAbstract;
use phpOMS\DataStorage\Session\HttpSession;
use phpOMS\Dispatcher\Dispatcher;
use phpOMS\Event\EventManager;
use phpOMS\Localization\L11nManager;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Module\ModuleAbstract;
use phpOMS\Module\ModuleManager;
use phpOMS\Router\WebRouter;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\TestUtils;
use Modules\Media\Models\Media;
use Modules\Profile\Models\Profile;
use Modules\Profile\Models\ProfileMapper;

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

        $this->app->dbPool          = $GLOBALS['dbpool'];
        $this->app->unitId          = 1;
        $this->app->accountManager  = new AccountManager($GLOBALS['session']);
        $this->app->appSettings     = new CoreSettings();
        $this->app->moduleManager   = new ModuleManager($this->app, __DIR__ . '/../../../../Modules/');
        $this->app->dispatcher      = new Dispatcher($this->app);
        $this->app->eventManager    = new EventManager($this->app->dispatcher);
        $this->app->eventManager->importFromFile(__DIR__ . '/../../../../Web/Api/Hooks.php');
        $this->app->sessionManager = new HttpSession(36000);
        $this->app->l11nManager    = new L11nManager();

        $account = new Account();
        TestUtils::setMember($account, 'id', 1);

        $permission       = new AccountPermission();
        $permission->unit = 1;
        $permission->app  = 1;
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
        $media              = new Media();
        $media->createdBy   = new NullAccount(1);
        $media->description = 'desc';
        $media->setPath('Web/Backend/img/default-user.jpg');
        $media->size      = 11;
        $media->extension = 'png';
        $media->name      = 'Image';

        if (($profile = ProfileMapper::get()->where('account', 1)->execute())->id === 0) {
            $profile = new Profile();

            $profile->account  = AccountMapper::get()->where('id', 1)->execute();
            $profile->image    = $media;
            $profile->birthday =  new \DateTime('now');

            $id = ProfileMapper::create()->execute($profile);
            self::assertGreaterThan(0, $profile->id);
            self::assertEquals($id, $profile->id);
        } else {
            $profile->image    = $media;
            $profile->birthday =  new \DateTime('now');

            ProfileMapper::update()->with('image')->execute($profile);
        }

        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;

        $this->module->apiSessionCreate($request, $response);
        self::assertGreaterThan(0, $sId = $response->get('')['response']->id);

        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('session', (string) $sId);
        $request->setData('status', ClockingStatus::END);

        $this->module->apiSessionElementCreate($request, $response);
        self::assertGreaterThan(0, $response->get('')['response']->id);
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
        self::assertEquals(RequestStatusCode::R_403, $response->header->status);
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
