<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   HumanResourceTimeRecording
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\HumanResourceTimeRecording\Models\ClockingType;

$date = new \DateTime('now');

echo $this->data['nav']->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Status'); ?><i class="g-icon download btn end-xs">download</i></div>
            <div class="slider">
            <table id="employeeList" class="default sticky">
                <thead>
                <tr>
                    <td><?= $this->getHtml('Date'); ?>
                    <td><?= $this->getHtml('Type'); ?>
                    <td class="wf-100"><?= $this->getHtml('Employee'); ?>
                    <td><?= $this->getHtml('Start'); ?>
                    <td><?= $this->getHtml('Break'); ?>
                    <td><?= $this->getHtml('End'); ?>
                    <td><?= $this->getHtml('Total'); ?>
                <tbody>
                <?php foreach ($this->data['employees'] as $employee) :
                    $session = $this->data['sessions'][$employee->id] ?? null;
                ?>
                <tr>
                    <td><?= $session?->getStart()->format('Y-m-d') ?? $date->format('Y-m-d H:i:s'); ?>
                    <td><span class="tag"><?= $this->getHtml('CT' . ($session?->type ?? ClockingType::NO_DATA)); ?></span>
                    <td>
                        <?= $this->printHtml($employee->profile->account->name1); ?>,
                        <?= $this->printHtml($employee->profile->account->name2); ?>
                    <td><?= $session?->getStart()->format('H:i:s'); ?>
                    <td><?= $session !== null ? ((int) ($session->getBreak() / 3600)) . 'h' : ''; ?> <?= $session !== null ? ((int) ($session->getBreak() / 60) % 60) . 'm' : ''; ?>
                    <td><?= $session?->getEnd() !== null ? $session->getEnd()->format('H:i') : ''; ?>
                    <td><?= $session !== null ? ((int) ($session->getBusy() / 3600)) . 'h' : ''; ?> <?= $session !== null ? ((int) ($session->getBusy() / 60) % 60) . 'm' : ''; ?>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
