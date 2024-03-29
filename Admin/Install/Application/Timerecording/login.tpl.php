<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Template
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Web\Timerecording
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
$head = $this->head;
?>
<!DOCTYPE HTML>
<html lang="<?= $this->printHtml($this->response->header->l11n->language); ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <base href="<?= \phpOMS\Uri\UriFactory::build('{/base}'); ?>/">
    <meta name="theme-color" content="#9e51c5">
    <meta name="msapplication-navbutton-color" content="#9e51c5">
    <meta name="theme-color" content="#9e51c5">
    <meta name="description" content="<?= $this->getHtml(':meta', '0', '0'); ?>">
    <link rel="manifest" href="<?= \phpOMS\Uri\UriFactory::build('Web/Timerecording/manifest.json'); ?>">
    <link rel="shortcut icon" href="<?= \phpOMS\Uri\UriFactory::build('Web/Timerecording/img/favicon.ico'); ?>" type="image/x-icon">
    <?= $head->meta->render(); ?>
    <title><?= $this->printHtml($head->title); ?></title>
    <style><?= $head->renderStyle(); ?></style>
    <script><?= $head->renderScript(); ?></script>
    <?= $head->renderAssets(); ?>
    <style type="text/css">
        :root {
            --main-bg: #2e1a5a;
            --main-bg-hl: #9e51c5;

            --iborder: rgba(166, 135, 232, .4);
            --iborder-active: rgba(166, 135, 232, .7);
            --ipt-c: rgba(166, 135, 232, .6);
            --ipt-c-active: rgba(166, 135, 232, .8);

            --ipt-ico-c: rgba(166, 135, 232, .6);
            --ipt-ico-c-active: rgba(166, 135, 232, 1);

            --btn-main-bg: rgba(166, 135, 232, .6);
            --btn-main-bg-active: rgba(166, 135, 232, .8);
            --btn-main-c: rgba(255, 255, 255, .9);

            --txt-on-bg-c: rgba(255, 255, 255, 0.7);
        }

        html, body {
            height: 100%;
            font-family: 'Roboto', sans-serif;
            background-image: linear-gradient(var(--main-bg-hl), var(--main-bg));
            color: var(--txt-on-bg-c);
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
            flex-direction: column;
            font-weight: 300;
        }

        #login-container {
            width: 90%;
            max-width: 800px;
            margin: 0 auto;
        }

        #login-logo {
            height: 185px;
        }

        #login-logo img {
            width: 20%;
            min-width: 200px;
        }

        h1 {
            text-shadow: 2px 2px 3px rgba(0,0,0,0.3);
        }

        #login-logo {
            margin-bottom: 2rem;
        }

        #login-logo, #login-form {
            text-align: center;
        }

        #passwordLogin, #cameraLogin {
            text-align: left;
        }

        form {
            margin-bottom: 1rem;
            display: inline-block;
            text-align: center;
            width: 100%;
        }

        form label {
            text-shadow: none;
            color: var(--txt-on-bg-c);
            cursor: pointer;
        }

        form input[type=text],
        form input[type=password] {
            margin-bottom: .5rem;
            background: rgba(0, 0, 0, .15);
            border: 1px solid var(--iborder);
            text-shadow: none;
            box-shadow: none;
            color: var(--txt-on-bg-c);
            width: 100%;
            transition : border 500ms ease-out;
            outline: none;
            box-sizing: border-box;
            line-height: 1rem;
        }

        .inputWithIcon {
            position: relative;
        }

        .inputWithIcon input {
            padding-left: 2.5rem;
        }

        .inputWithIcon .frontIco {
            color: var(--ipt-ico-c);
            font-size: 1rem;
            position: absolute;
            left: 0;
            top: 0;
            padding: .65rem;
        }

        .inputWithIcon .endIco {
            color: var(--ipt-ico-c);
            font-size: 1rem;
            position: absolute;
            right: 0;
            top: 0;
            padding: .65rem;
        }

        form input[type=text]:active, form input[type=text]:focus,
        form input[type=password]:active, form input[type=password]:focus {
            border: 1px solid var(--iborder-active);
            color: var(--txt-on-bg-c);
        }

        form input[type=text]:active~.frontIco, form input[type=text]:focus~.frontIco,
        form input[type=password]:active~.frontIco, form input[type=password]:focus~.frontIco,
        form input[type=text]:active~.endIco, form input[type=text]:focus~.endIco,
        form input[type=password]:active~.endIco, form input[type=password]:focus~.endIco {
            color: var(--ipt-ico-c-active);
        }

        form input[type=text]~.endIco, form input[type=text]~.endIco,
        form input[type=password]~.endIco, form input[type=password]~.endIco {
            cursor: pointer;
        }

        form input[type=submit], button {
            width: calc(50% - 10px);
            background-color: var(--btn-main-bg);
            border: none;
            text-shadow: none;
            box-shadow: none;
            color: var(--btn-main-c);
            cursor: pointer;
            transition : background-color 500ms ease-out;
            margin-bottom: 1rem;
            white-space: nowrap;
        }

        button+button, input+button {
            margin-left: 14px;
        }

        form input[type=submit]:hover, button:hover,
        form input[type=submit]:focus, button:focus {
            background-color: var(--btn-main-bg-active);
            border: none;
            text-shadow: none;
            box-shadow: none;
        }

        #forgot-password {
            text-align: center;
        }

        #forgot-password a {
            padding-bottom: .5rem;
            cursor: pointer;
            transition : border-bottom 100ms ease-out;
        }

        #forgot-password a:hover,
        #forgot-password a:focus {
            color: rgba(255, 255, 255, .8);
            border-bottom: 1px solid rgba(255, 255, 255, .6);
        }

        video {
            width: 100%;
            height: 100%;
            border: 1px solid var(--iborder);
        }
    </style>
</head>
<body>
<div id="login-container">
    <div id="login-logo">
        <img class="animated infinte pulse" alt="<?= $this->getHtml('Logo', '0', '0'); ?>" src="<?= \phpOMS\Uri\UriFactory::build('Web/Backend/img/logo.png'); ?>">
    </div>
    <div id="login-form">
        <form id="login" method="POST" action="<?= \phpOMS\Uri\UriFactory::build('{/api}login?{?}'); ?>">
            <button id="iCameraLoginButton" name="cameraLoginButton" type="button" tabindex="1"><?= $this->getHtml('CameraLogin', '0', '0'); ?></button>
            <button id="iPasswordLoginButton" name="passwordLoginButton" type="button" tabindex="2"><?= $this->getHtml('PasswordLogin', '0', '0'); ?></button>
            <div id="cameraLogin" class="vh">
                <h1><?= $this->getHtml('IDCard', '0', '0'); ?>:</h1>
                <video id="iVideoCanvas"></video>
                <button class="cancelButton" name="cancelButton" type="button" tabindex="6"><?= $this->getHtml('Cancel', '0', '0'); ?></button>
                <div id="iCameraCountdown"><?php \printf($this->getHtml('TimerCamera', '0', '0'), '<span id="iCameraCountdownClock"></span>'); ?></div>
            </div>
            <div id="passwordLogin" class="vh">
            <h1><?= $this->getHtml('Login', '0', '0'); ?>:</h1>
                <label for="iName"><?= $this->getHtml('Username', '0', '0'); ?>:</label>
                <div class="inputWithIcon">
                    <i class="frontIco g-icon" aria-hidden="true">person</i>
                    <input id="iName" type="text" name="user" tabindex="3" value="admin" autofocus>
                    <i class="endIco g-icon close" aria-hidden="true">close</i>
                </div>
                <label for="iPassword"><?= $this->getHtml('Password', '0', '0'); ?>:</label>
                <div class="inputWithIcon">
                    <i class="frontIco g-icon" aria-hidden="true">lock</i>
                    <input id="iPassword" type="password" name="pass" tabindex="4" value="orange">
                    <i class="endIco g-icon close" aria-hidden="true">close</i>
                </div>
                <input id="iLoginButton" name="loginButton" type="submit" value="<?= $this->getHtml('Login', '0', '0'); ?>" tabindex="5">
                <button class="cancelButton" name="cancelButton" type="button" tabindex="6"><?= $this->getHtml('Cancel', '0', '0'); ?></button>
                <div id="iPasswordCountdown"><?php \printf($this->getHtml('TimerLogin', '0', '0'), '<span id="iPasswordCountdownClock"></span>'); ?></div>
            </div>
        </form>
    </div>
</div>
<?= $head->renderAssetsLate(); ?>
