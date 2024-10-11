<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\HumanResourceTimeRecording\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\HumanResourceTimeRecording\Admin;

use phpOMS\Application\ApplicationAbstract;
use phpOMS\Config\SettingsInterface;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Module\InstallerAbstract;
use phpOMS\Module\ModuleInfo;

/**
 * Installer class.
 *
 * @package Modules\HumanResourceTimeRecording\Admin
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class Installer extends InstallerAbstract
{
    /**
     * Path of the file
     *
     * @var string
     * @since 1.0.0
     */
    public const PATH = __DIR__;

    /**
     * {@inheritdoc}
     */
    public static function install(ApplicationAbstract $app, ModuleInfo $info, SettingsInterface $cfgHandler) : void
    {
        parent::install($app, $info, $cfgHandler);

        /* Clocking types */
        $fileContent = \file_get_contents(__DIR__ . '/Install/types.json');
        if ($fileContent === false) {
            return;
        }

        /** @var array $types */
        $types = \json_decode($fileContent, true);
        if ($types === false) {
            return;
        }

        self::createClockingTypes($app, $types);
    }

    /**
     * Install default bill types
     *
     * @param ApplicationAbstract $app   Application
     * @param array               $types Clocking types
     *
     * @return array
     *
     * @since 1.0.0
     */
    private static function createClockingTypes(ApplicationAbstract $app, array $types) : array
    {
        $billTypes = [];

        /** @var \Modules\HumanResourceTimeRecording\Controller\ApiClockingTypeController $module */
        $module = $app->moduleManager->get('HumanResourceTimeRecording', 'ApiClockingType');

        foreach ($types as $type) {
            $response = new HttpResponse();
            $request  = new HttpRequest();

            $request->header->account = 1;
            $request->setData('name', $type['name'] ?? '');
            $request->setData('is_work', $type['is_work'] ?? false);
            $request->setData('content', \reset($type['l11n']));
            $request->setData('language', \array_keys($type['l11n'])[0] ?? 'en');

            $module->apiClockingTypeCreate($request, $response);

            $responseData = $response->getData('');
            if (!\is_array($responseData)) {
                continue;
            }

            $billType = \is_array($responseData['response'])
                ? $responseData['response']
                : $responseData['response']->toArray();

            $billTypes[] = $billType;

            $isFirst = true;
            foreach ($type['l11n'] as $language => $l11n) {
                if ($isFirst) {
                    $isFirst = false;
                    continue;
                }

                $response = new HttpResponse();
                $request  = new HttpRequest();

                $request->header->account = 1;
                $request->setData('content', $l11n);
                $request->setData('language', $language);
                $request->setData('ref', $billType['id']);

                $module->apiClockingTypeL11nCreate($request, $response);
            }
        }

        return $billTypes;
    }
}
