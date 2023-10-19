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

$sessions = $this->data['sessions'];

echo $this->data['nav']->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="box wf-100">
        <table id="accountList" class="default">
                <caption><?= $this->getHtml('Recordings'); ?><i class="g-icon end-xs download btn">download</i></caption>
                <thead>
                <tr>
                    <td><?= $this->getHtml('Date'); ?>
                    <td><?= $this->getHtml('Type'); ?>
                    <td><?= $this->getHtml('Employee'); ?>
                    <td><?= $this->getHtml('Start'); ?>
                    <td><?= $this->getHtml('Break'); ?>
                    <td><?= $this->getHtml('End'); ?>
                    <td><?= $this->getHtml('Total'); ?>
                <tbody>
                <?php foreach ($sessions as $session) : ?>
                <tr>
                    <td><?= $session->getStart()->format('Y-m-d'); ?>
                    <td><span class="tag"><?= $this->getHtml('CT' . $session->getType()); ?></span>
                    <td>
                        <?= $this->printHtml($session->getEmployee()->profile->account->name1); ?>,
                        <?= $this->printHtml($session->getEmployee()->profile->account->name2); ?>
                    <td><?= $session->getStart()->format('H:i:s'); ?>
                    <td><?= (int) ($session->getBreak() / 3600); ?>h <?= ((int) ($session->getBreak() / 60) % 60); ?>m
                    <td><?= $session->getEnd() !== null ? $session->getEnd()->format('H:i') : ''; ?>
                    <td><?= (int) ($session->getBusy() / 3600); ?>h <?= ((int) ($session->getBusy() / 60) % 60); ?>m
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
