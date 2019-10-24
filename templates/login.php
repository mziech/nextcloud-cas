<?php
/** @var $l \OCP\IL10N */
/** @var $_ array */

script('cas', 'login');
?>

<?php if (isset($_["errorCode"]) && $_["errorCode"] === "FORBIDDEN") { ?>
    <div class="warning">
        <?php p($l->t('This service is restriced!')); ?><br>
        <small><?php p($l->t('You are not allowed to login to this service.')); ?></small>
    </div>
<?php } else if (isset($_["errorCode"]) && $_["errorCode"] === "INVALID_SERVICE") { ?>
    <div class="warning">
        <?php p($l->t('Unknown service!')); ?><br>
        <small><?php p($l->t('You tried to login to an unknown service, please contact the administrator.')); ?></small>
    </div>
<?php } else if (isset($_["errorCode"])) { ?>
    <div class="warning">
        <?php p($l->t('Internal server error!')); ?><br>
        <small><?php p($l->t('Due to technical problems, your login request cannot be processed right now. Please try again later or contact the administrator.')); ?></small>
    </div>
<?php } ?>

<?php
if (isset($_["ticket"]) && isset($_["service"])) {
    if ($_["method"] === "POST") {
        ?>

        <div class="warning">
            <?php p($l->t('CAS login successful')); ?><br>
            <form id="cas-login-redirect" action="<?php p($_["service"]); ?>" method="post">
                <input type="hidden" name="token" value="<?php p($_["ticket"]->getTicket()); ?>">
                <button type="submit"><?php p($l->t('If you are not being redirected, please click here.')); ?></button>
            </form>
        </div>

    <?php } else {
        $redirectUrl = $_["service"] . (strpos($_["service"], "?") === FALSE ? '?' : '&') . "ticket=" . $_["ticket"]->getTicket();
        ?>

        <div class="warning">
            <?php p($l->t('CAS login successful')); ?><br>
            <a id="cas-login-redirect"
               href="<?php p($redirectUrl); ?>"><?php p($l->t('If you are not being redirected, please click here.')); ?></a>
        </div>

    <?php } ?>
<?php } ?>

