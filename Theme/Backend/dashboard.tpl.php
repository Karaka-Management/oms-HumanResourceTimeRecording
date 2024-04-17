<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   HumanResourceTimeRecording
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\HumanResourceTimeRecording\Models\ClockingType;
use phpOMS\Uri\UriFactory;

$date = new \DateTime('now');

echo $this->data['nav']->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
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

                    // @todo Implement clocking view per employee for HR (also to edit clocking)
                    $employeeUrl = UriFactory::build('{/base}/humanresource/staff/view?id=' . $employee->id);
                ?>
                <tr>
                    <td><?= $session?->start->format('Y-m-d  H:i:s') ?? $date->format('Y-m-d H:i:s'); ?>
                    <td><span class="tag"><?= $this->getHtml('CT' . ($session?->type ?? ClockingType::NO_DATA)); ?></span>
                    <td><a class="content" href="<?= $employeeUrl; ?>">
                        <?= $this->printHtml($employee->profile->account->name1); ?>,
                        <?= $this->printHtml($employee->profile->account->name2); ?>
                        </a>
                    <td><?= $session?->start->format('H:i:s'); ?>
                    <td><?= $session !== null ? ((int) ($session->getBreak() / 3600)) . 'h' : ''; ?> <?= $session !== null ? ((int) ($session->getBreak() / 60) % 60) . 'm' : ''; ?>
                    <td><?= $session?->end?->format('H:i') ?? ''; ?>
                    <td><?= $session !== null ? ((int) ($session->busy / 3600)) . 'h' : ''; ?> <?= $session !== null ? ((int) ($session->busy / 60) % 60) . 'm' : ''; ?>
                <?php endforeach; ?>
            </table>
            </div>
        </section>
    </div>
</div>
