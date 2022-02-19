<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   HumanResourceTimeRecording
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

/** @var \Modules\HumanResourceTimeRecording\Models\Session $session */
$session  = $this->getData('session');
$elements = $session->getSessionElements();

echo $this->getData('nav')->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="box wf-100">
        <table id="accountList" class="default">
                <caption><?= $session->start->format('Y-m-d'); ?><i class="fa fa-download floatRight download btn"></i></caption>
                <thead>
                <tr>
                    <td><?= $this->getHtml('Status'); ?>
                    <td class="wf-100"><?= $this->getHtml('Time'); ?>
                    <td class="wf-100"><?= $this->getHtml('Date'); ?>
                <tbody>
                <?php foreach ($elements as $element) : ?>
                <tr>
                    <td><?= $this->getHtml('CS' . $element->getStatus()); ?>
                    <td><?= $element->datetime->format('H:i:s'); ?>
                    <td><?= $element->datetime->format('Y-m-d'); ?>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>