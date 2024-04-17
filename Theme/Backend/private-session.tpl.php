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
$elements = $session->sessionElements;

echo $this->data['nav']->render(); ?>
<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head"><?= $session->start->format('Y-m-d'); ?></div>
            <table id="sessionList" class="default sticky">
                <thead>
                <tr>
                    <td><?= $this->getHtml('Status'); ?>
                    <td class="wf-100"><?= $this->getHtml('Time'); ?>
                    <td class="wf-100"><?= $this->getHtml('Date'); ?>
                <tbody>
                <?php
                $c = 0;
                foreach ($elements as $element) :
                    ++$c;
                ?>
                <tr>
                    <td><?= $this->getHtml('CS' . $element->status); ?>
                    <td><?= $element->datetime->format('H:i:s'); ?>
                    <td><?= $element->datetime->format('Y-m-d'); ?>
                <?php endforeach; ?>
                <?php if ($c === 0) : ?>
                <tr><td colspan="3" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
        </section>
    </div>
</div>