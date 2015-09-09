<?php

use app\language\LanguageManager;
use app\mail\MailManager;
use app\mail\verification\MailVerification;
use app\mail\verification\MailVerificationManager;
use app\registry\Registry;
use app\session\SessionManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;
use app\template\PageSidebarBuilder;
use app\user\User;
use app\user\UserManager;
use app\util\AccountUtils;

// Include the page top
require_once('top.php');

/** Registry key to define if users are allowed to login with their username. */
const REG_ACCOUNT_LOGIN_ALLOW_USERNAME = 'account.login.allowUsername';
/** Registry key to define if users are allowed to login with their mail address. */
const REG_ACCOUNT_LOGIN_ALLOW_MAIL = 'account.login.allowMail';

// Make sure the user isn't logged in
if(SessionManager::isLoggedIn()) {
    // Redirect the user to the front page
    header('Location: index.php');
    die();
}

// Check whether the user is trying to login, if not show the login form instead
if(!isset($_POST['login_user']) || !isset($_POST['login_password'])) {
    // Get the default user
    $userValue = '';
    if(isset($_GET['user']))
        $userValue = trim($_GET['user']);

    // Determine whether to show a back button
    $showBackButton = true;
    if(isset($_GET['back']))
        $showBackButton = $_GET['back'] == 1;

    ?>
    <div data-role="page" id="page-login">
        <?php PageHeaderBuilder::create(__('account', 'login'))->setBackButton($showBackButton ? 'index.php' : null)->build(); ?>

        <div data-role="main" class="ui-content">
            <p><?= __('login', 'enterUsernamePasswordToLogin'); ?></p><br />

            <form method="POST" action="login.php?a=login">
                <input type="text" name="login_user" value="<?=$userValue; ?>" placeholder="<?= __('account', 'username'); ?>" />
                <input type="password" name="login_password" value="" placeholder="<?= __('account', 'password'); ?>" /><br />

                <input type="submit" value="<?= __('account', 'login'); ?>" class="ui-btn ui-icon-lock ui-btn-icon-right" />
            </form>
        </div>

        <?php
        // Build the footer and sidebar
        PageFooterBuilder::create()->build();
        PageSidebarBuilder::create()->build();
        ?>
    </div>
    <?php

} else {
    // Get the username/email and password
    $loginUser = trim($_POST['login_user']);
    $loginPassword = $_POST['login_password'];

    // Define a variable to get the user
    $user = null;

    // Check whether a user exists with this username
    if(Registry::getValue(REG_ACCOUNT_LOGIN_ALLOW_USERNAME)->getBoolValue() && UserManager::isUserWithUsername($loginUser))
        $user = UserManager::getUserWithUsername($loginUser);

    elseif(Registry::getValue(REG_ACCOUNT_LOGIN_ALLOW_MAIL)->getBoolValue() && AccountUtils::isValidMail($loginUser)) {
        // Check whether this mail is registered and verified
        if(MailManager::isMailWithMail($loginUser)) {
            // Get the mail of the user
            $mail = MailManager::getMailWithMail($loginUser);

            // Get the corresponding user if valid
            if($mail !== null)
                $user = $mail->getUser();

        } else {
            // Get all mails waiting for verification for this user
            $mails = MailVerificationManager::getMailVerificationsWithMail($loginUser);

            // Get the user of the unverified mail if one address is returned
            if(sizeof($mails) == 1) {
                // Get the mail verification
                $mailVerification = $mails[0];

                // Validate the instance and get the user
                if($mailVerification instanceof MailVerification)
                    $user = $mailVerification->getUser();
            }
        }
    }

    // Make sure a user is found
    if(!($user instanceof User))
        showErrorPage(__('login', 'usernameOrPasswordIncorrect'));

    // Validate the password
    if(!$user->isPassword($loginPassword))
        showErrorPage(__('login', 'usernameOrPasswordIncorrect'));

    // Get and use the user's language if set
    if(($userLang = LanguageManager::getUserLanguageTag($user)) !== null)
        LanguageManager::setLanguageTag($userLang, true, true, false);

    // Create a session for the user
    if(!SessionManager::createSession($user))
        showErrorPage();

    ?>
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create()->build(); ?>

        <div data-role="main" class="ui-content">
            <p>
                <?= __('general', 'welcome'); ?> <?=$user->getFullName(); ?>!<br />
                <br />
                <?= __('login', 'loginSuccess'); ?>
            </p>
            <br />

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="index.php" data-ajax="false"
                   class="ui-btn ui-icon-carat-r ui-btn-icon-left"><?= __('navigation', 'continue'); ?></a>
            </fieldset>
        </div>

        <?php
        // Build the footer and sidebar
        PageFooterBuilder::create()->build();
        PageSidebarBuilder::create()->build();
        ?>
    </div>
    <?php
}

// Include the page bottom
require_once('bottom.php');