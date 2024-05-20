<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   HumanResourceTimeRecording
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use \Modules\HumanResourceTimeRecording\Models\ClockingStatus;
use \Modules\HumanResourceTimeRecording\Models\ClockingType;
use \phpOMS\Stdlib\Base\SmartDateTime;
use phpOMS\Uri\UriFactory;

/** @var \Modules\HumanResourceTimeRecording\Models\Session[] $sessions */
$sessions     = $this->data['sessions'];
$sessionCount = \count($sessions);

/** @var \Modules\HumanResourceTimeRecording\Models\Session $lastOpenSession */
$lastOpenSession = $this->data['lastSession'];

$type   = $lastOpenSession !== null ? $lastOpenSession->type : ClockingType::OFFICE;
$status = $lastOpenSession !== null ? $lastOpenSession->getStatus() : ClockingStatus::END;

/** @var \phpOMS\Stdlib\Base\SmartDateTime $startWeek */
$startWeek = new SmartDateTime('now');
$startWeek = $startWeek->getStartOfWeek();
$endWeek   = $startWeek->createModify(0, 0, 6);

$startMonth = new SmartDateTime('now');
$startMonth = $startMonth->getStartOfMonth();
$endMonth   = $startMonth->createModify(0, 1, -1);

$busy = [
    'total' => 0,
    'month' => 0,
    'week'  => 0,
];

echo $this->data['nav']->render(); ?>
<div class="row">
    <div class="col-md-4 col-xs-12">
        <section class="portlet">
            <div class="portlet-body">
                <form id="iClocking" method="PUT" action="<?= UriFactory::build('{/api}humanresource/timerecording/element?{?}&csrf={$CSRF}'); ?>">
                    <table class="layout wf-100" style="table-layout: fixed">
                        <tr><td><label for="iType"><?= $this->getHtml('Type'); ?></label>
                        <tr><td>
                            <select id="iType" name="type">
                                <option value="<?= ClockingType::OFFICE; ?>"<?= $type === ClockingType::OFFICE ? ' selected': ''; ?>><?= $this->getHtml(':CT1'); ?>
                                <option value="<?= ClockingType::REMOTE; ?>"<?= $type === ClockingType::REMOTE ? ' selected': ''; ?>><?= $this->getHtml(':CT3'); ?>
                                <option value="<?= ClockingType::HOME; ?>"<?= $type === ClockingType::HOME ? ' selected': ''; ?>><?= $this->getHtml(':CT2'); ?>
                                <option value="<?= ClockingType::VACATION; ?>"<?= $type === ClockingType::VACATION ? ' selected': ''; ?>><?= $this->getHtml(':CT4'); ?>
                                <option value="<?= ClockingType::SICK; ?>"<?= $type === ClockingType::SICK ? ' selected': ''; ?>><?= $this->getHtml(':CT5'); ?>
                                <option value="<?= ClockingType::ON_THE_MOVE; ?>"<?= $type === ClockingType::ON_THE_MOVE ? ' selected': ''; ?>><?= $this->getHtml(':CT6'); ?>
                            </select>
                        <tr><td><label for="iStatus"><?= $this->getHtml(':Status'); ?></label>
                        <tr><td>
                            <select id="iStatus" name="status">
                                <option value="<?= ClockingStatus::START; ?>"<?= $status === ClockingStatus::END ? ' selected' : ''; ?>><?= $this->getHtml(':CS1'); ?>
                                <option value="<?= ClockingStatus::PAUSE; ?>"<?= $status === ClockingStatus::START ? ' selected' : ''; ?>><?= $this->getHtml(':CS2'); ?>
                                <option value="<?= ClockingStatus::CONTINUE; ?>"<?= $status === ClockingStatus::PAUSE ? ' selected' : ''; ?>><?= $this->getHtml(':CS3'); ?>
                                <option value="<?= ClockingStatus::END; ?>"<?= $status === ClockingStatus::CONTINUE ? ' selected' : ''; ?>><?= $this->getHtml(':CS4'); ?>
                            </select>
                        <tr><td>
                            <input type="hidden" name="session" value="<?= $lastOpenSession !== null ? $lastOpenSession->id : ''; ?>">
                            <input type="submit" id="iclockingButton" name="clockingButton" value="<?= $this->getHtml('Submit', '0', '0'); ?>" data-action='[
                                    {
                                        "key": 1, "listener": "click", "action": [
                                            {"key": 1, "type": "redirect", "uri": "{%}", "delay": 3000}
                                        ]
                                    }
                                ]'>
                    </table>
                </form>
            </div>
        </section>
    </div>

    <div class="col-md-4 col-xs-12">
        <section class="portlet">
            <div class="portlet-head">Work</div>
            <div class="portlet-body">
                <table>
                    <tr><td>This month<td>
                    <tr><td>Last month<td>
                    <tr><td>This year<td>
                </table>
            </div>
        </section>
    </div>

    <div class="col-md-4 col-xs-12">
        <section class="portlet">
            <div class="portlet-head">Vacation</div>
            <div class="portlet-body">
                <table>
                    <tr><td>Used Vacation<td>
                    <tr><td>Last Vacation<td>
                    <tr><td>Next Vacation<td>
                </table>
            </div>
        </section>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Recordings', 'HumanResourceTimeRecording', 'Backend'); ?><i class="g-icon download btn end-xs">download</i></div>
            <div class="slider">
            <table id="recordingList" class="default sticky">
                <thead>
                <tr>
                    <td><?= $this->getHtml('Date', 'HumanResourceTimeRecording', 'Backend'); ?>
                        <label for="recordingList-sort-1">
                            <input type="radio" name="recordingList-sort" id="recordingList-sort-1">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="recordingList-sort-2">
                            <input type="radio" name="recordingList-sort" id="recordingList-sort-2">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Type', 'HumanResourceTimeRecording', 'Backend'); ?>
                        <label for="recordingList-sort-3">
                            <input type="radio" name="recordingList-sort" id="recordingList-sort-3">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="recordingList-sort-4">
                            <input type="radio" name="recordingList-sort" id="recordingList-sort-4">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Status', 'HumanResourceTimeRecording', 'Backend'); ?>
                        <label for="recordingList-sort-5">
                            <input type="radio" name="recordingList-sort" id="recordingList-sort-5">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="recordingList-sort-6">
                            <input type="radio" name="recordingList-sort" id="recordingList-sort-6">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Start', 'HumanResourceTimeRecording', 'Backend'); ?>
                        <label for="recordingList-sort-7">
                            <input type="radio" name="recordingList-sort" id="recordingList-sort-7">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="recordingList-sort-8">
                            <input type="radio" name="recordingList-sort" id="recordingList-sort-8">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Break', 'HumanResourceTimeRecording', 'Backend'); ?>
                        <label for="recordingList-sort-9">
                            <input type="radio" name="recordingList-sort" id="recordingList-sort-9">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="recordingList-sort-10">
                            <input type="radio" name="recordingList-sort" id="recordingList-sort-10">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('End', 'HumanResourceTimeRecording', 'Backend'); ?>
                        <label for="recordingList-sort-11">
                            <input type="radio" name="recordingList-sort" id="recordingList-sort-11">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="recordingList-sort-12">
                            <input type="radio" name="recordingList-sort" id="recordingList-sort-12">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Total', 'HumanResourceTimeRecording', 'Backend'); ?>
                        <label for="recordingList-sort-13">
                            <input type="radio" name="recordingList-sort" id="recordingList-sort-13">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="recordingList-sort-14">
                            <input type="radio" name="recordingList-sort" id="recordingList-sort-14">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                <tbody>
                <?php
                    $count = 0;
                    foreach ($this->data['sessions'] as $session) : ++$count;
                    $url = UriFactory::build('{/base}/private/timerecording/session?{?}&id=' . $session->id);
                ?>
                <tr data-href="<?= $url; ?>">
                    <td><a href="<?= $url; ?>">
                        <?php
                            if ($this->data['lastSession'] !== null
                                && $session->start->format('Y-m-d') === $this->data['lastSession']->start->format('Y-m-d')
                            ) : ?>
                            <span class="tag">Today</span>
                        <?php else : ?>
                            <?= $session->start->format('Y-m-d'); ?> - <?= $this->getHtml(':D' . $session->start->format('w'), 'HumanResourceTimeRecording', 'Backend'); ?>
                        <?php endif; ?></a>
                    <td><a href="<?= $url; ?>"><span class="tag"><?= $this->getHtml(':CT' . $session->type, 'HumanResourceTimeRecording', 'Backend'); ?></span></a>
                    <td><a href="<?= $url; ?>"><span class="tag"><?= $this->getHtml(':CS' . $session->getStatus(), 'HumanResourceTimeRecording', 'Backend'); ?></span></a>
                    <td><a href="<?= $url; ?>"><?= $session->start->format('H:i'); ?></a>
                    <td><a href="<?= $url; ?>"><?= (int) ($session->getBreak() / 3600); ?>h <?= ((int) ($session->getBreak() / 60) % 60); ?>m</a>
                    <td><a href="<?= $url; ?>"><?= $session->end?->format('H:i'); ?></a>
                    <td><a href="<?= $url; ?>"><?= (int) ($session->busy / 3600); ?>h <?= ((int) ($session->busy / 60) % 60); ?>m</a>
                <?php
                    $busy['week'] += $session->busy;
                    if ($session->start->getTimestamp() < $startWeek->getTimestamp()
                        || $count === $sessionCount
                ) : ?>
                <tr>
                    <th colspan="6"> <?= $startWeek->format('Y/m/d'); ?> - <?= $endWeek->format('Y/m/d'); ?>
                    <th><?= (int) ($busy['week'] / 3600); ?>h <?= ((int) ($busy['week'] / 60) % 60); ?>m
                <?php
                        $endWeek      = $startWeek->createModify(0, 0, -1);
                        $startWeek    = $startWeek->createModify(0, 0, -7);
                        $busy['week'] = 0;
                    endif;
                ?>
                <?php
                    $busy['month'] += $session->busy;
                    if ($session->start->getTimestamp() < $startMonth->getTimestamp()
                        || $count === $sessionCount
                ) : ?>
                <tr>
                    <th colspan="6"> <?= $startMonth->format('Y/m/d'); ?> - <?= $endMonth->format('Y/m/d'); ?>
                    <th><?= (int) ($busy['month'] / 3600); ?>h <?= ((int) ($busy['month'] / 60) % 60); ?>m
                <?php
                        $endMonth      = $startMonth->createModify(0, 0, -1);
                        $startMonth    = $startMonth->createModify(0, -1, 0);
                        $busy['month'] = 0;
                    endif;
                ?>
                <?php endforeach; ?>
                <?php if ($count === 0) : ?>
                <tr>
                    <td colspan="7" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
            </div>
        </section>
    </div>
</div>