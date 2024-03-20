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

/** @var \Modules\HumanResourceTimeRecording\Models\Session $session */
$session  = $this->data['session'];
$elements = $session->getSessionElements();

echo $this->data['nav']->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="box wf-100">
            <table id="sessionList" class="default sticky">
                <caption><?= $session->start->format('Y-m-d'); ?><i class="g-icon end-xs download btn">download</i></caption>
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