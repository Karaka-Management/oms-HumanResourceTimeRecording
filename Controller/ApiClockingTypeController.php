<?php

/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\HumanResourceTimeRecording
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\HumanResourceTimeRecording\Controller;

use Modules\HumanResourceTimeRecording\Models\ClockingType;
use Modules\HumanResourceTimeRecording\Models\ClockingTypeL11nMapper;
use Modules\HumanResourceTimeRecording\Models\ClockingTypeMapper;
use phpOMS\Localization\BaseStringL11n;
use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;

/**
 * HumanResourceTimeRecording class.
 *
 * @package Modules\HumanResourceTimeRecording
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class ApiClockingTypeController extends Controller
{
    /**
     * Api method to create clocking type
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiClockingTypeCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateClockingTypeCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $clockingType = $this->createClockingTypeFromRequest($request);
        $this->createModel($request->header->account, $clockingType, ClockingTypeMapper::class, 'clocking_type', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $clockingType);
    }

    /**
     * Method to create ClockingType from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return ClockingType
     *
     * @since 1.0.0
     */
    private function createClockingTypeFromRequest(RequestAbstract $request) : ClockingType
    {
        $clockingType = new ClockingType($request->getDataString('name') ?? '');
        $clockingType->isWork = $request->getDataBool('is_work') ?? false;
        $clockingType->customFutureTimeAllowed = $request->getDataBool('custom_future_time_allowed') ?? false;
        $clockingType->customPastTimeAllowed = $request->getDataBool('custom_past_time_allowed') ?? false;
        $clockingType->correctionAllowed = $request->getDataBool('correction_allowed') ?? false;
        $clockingType->setL11n(
            $request->getDataString('title') ?? '',
            ISO639x1Enum::tryFromValue($request->getDataString('language')) ?? ISO639x1Enum::_EN
        );

        return $clockingType;
    }

    /**
     * Validate ClockingType create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateClockingTypeCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['title'] = !$request->hasData('title'))
            || ($val['name'] = !$request->hasData('name'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create ClockingType l11n
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiClockingTypeL11nCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateClockingTypeL11nCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $clockingTypeL11n = $this->createClockingTypeL11nFromRequest($request);
        $this->createModel($request->header->account, $clockingTypeL11n, ClockingTypeL11nMapper::class, 'clocking_type_l11n', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $clockingTypeL11n);
    }

    /**
     * Method to create ClockingType l11n from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return BaseStringL11n
     *
     * @since 1.0.0
     */
    private function createClockingTypeL11nFromRequest(RequestAbstract $request) : BaseStringL11n
    {
        $clockingTypeL11n           = new BaseStringL11n();
        $clockingTypeL11n->ref      = $request->getDataInt('type') ?? 0;
        $clockingTypeL11n->language = ISO639x1Enum::tryFromValue($request->getDataString('language')) ?? $request->header->l11n->language;
        $clockingTypeL11n->content  = $request->getDataString('title') ?? '';

        return $clockingTypeL11n;
    }

    /**
     * Validate ClockingType l11n create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateClockingTypeL11nCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['title'] = !$request->hasData('title'))
            || ($val['type'] = !$request->hasData('type'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to update ClockingType
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiClockingTypeUpdate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateClockingTypeUpdate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidUpdateResponse($request, $response, $val);

            return;
        }

        /** @var ClockingType $old */
        $old = ClockingTypeMapper::get()->where('id', (int) $request->getData('id'));
        $new = $this->updateClockingTypeFromRequest($request, clone $old);

        $this->updateModel($request->header->account, $old, $new, ClockingTypeMapper::class, 'clocking_type', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
    }

    /**
     * Method to update ClockingType from request.
     *
     * @param RequestAbstract $request Request
     * @param ClockingType        $new     Model to modify
     *
     * @return ClockingType
     *
     * @todo Implement API update function
     *
     * @since 1.0.0
     */
    public function updateClockingTypeFromRequest(RequestAbstract $request, ClockingType $new) : ClockingType
    {
        return $new;
    }

    /**
     * Validate ClockingType update request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @todo Implement API validation function
     *
     * @since 1.0.0
     */
    private function validateClockingTypeUpdate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to delete ClockingType
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiClockingTypeDelete(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateClockingTypeDelete($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidDeleteResponse($request, $response, $val);

            return;
        }

        /** @var \Modules\HumanResourceTimeRecording\Models\ClockingType $clockingType */
        $clockingType = ClockingTypeMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $clockingType, ClockingTypeMapper::class, 'clocking_type', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $clockingType);
    }

    /**
     * Validate ClockingType delete request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateClockingTypeDelete(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to update ClockingTypeL11n
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiClockingTypeL11nUpdate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateClockingTypeL11nUpdate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidUpdateResponse($request, $response, $val);

            return;
        }

        /** @var BaseStringL11n $old */
        $old = ClockingTypeL11nMapper::get()->where('id', (int) $request->getData('id'));
        $new = $this->updateClockingTypeL11nFromRequest($request, clone $old);

        $this->updateModel($request->header->account, $old, $new, ClockingTypeL11nMapper::class, 'clocking_type_l11n', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
    }

    /**
     * Method to update ClockingTypeL11n from request.
     *
     * @param RequestAbstract $request Request
     * @param BaseStringL11n  $new     Model to modify
     *
     * @return BaseStringL11n
     *
     * @since 1.0.0
     */
    public function updateClockingTypeL11nFromRequest(RequestAbstract $request, BaseStringL11n $new) : BaseStringL11n
    {
        $new->ref      = $request->getDataInt('type') ?? $new->ref;
        $new->language = ISO639x1Enum::tryFromValue($request->getDataString('language')) ?? $new->language;
        $new->content  = $request->getDataString('title') ?? $new->content;

        return $new;
    }

    /**
     * Validate ClockingTypeL11n update request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateClockingTypeL11nUpdate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to delete ClockingTypeL11n
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiClockingTypeL11nDelete(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateClockingTypeL11nDelete($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidDeleteResponse($request, $response, $val);

            return;
        }

        /** @var BaseStringL11n $clockingTypeL11n */
        $clockingTypeL11n = ClockingTypeL11nMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $clockingTypeL11n, ClockingTypeL11nMapper::class, 'clocking_type_l11n', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $clockingTypeL11n);
    }

    /**
     * Validate ClockingTypeL11n delete request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateClockingTypeL11nDelete(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))) {
            return $val;
        }

        return [];
    }
}
