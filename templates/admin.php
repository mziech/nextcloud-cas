<?php
/** @var $l \OCP\IL10N */
/** @var $_ array */

script('cas', 'settings');
style('cas', 'settings');

?>

<div id="cas" class="section">
    <h2><?php p($l->t("CAS Server"))?></h2>
    <p class="settings-hint"><?php p($l->t("Client should be configured to use the following CAS base URL: "))?></p>
    <h3><?php p($l->t("Service Providers"))?></h3>
    <div id="cas-clients" class="hidden">
        <table class="grid">
            <thead>
            <tr>
                <th><?php p($l->t("ID"))?></th>
                <th><?php p($l->t("URL"))?></th>
                <th><?php p($l->t("URL match type"))?></th>
                <th><?php p($l->t("Groups"))?></th>
                <th></th>
            </tr>
            </thead>
            <tbody id="cas-services">
            </tbody>
        </table>

        <button id="cas-add-service" class="button"><?php p($l->t("Add service provider"))?></button>
        <button id="cas-save" class="button primary"><?php p($l->t("Save"))?></button>
        <span id="cas-saving" class="hidden">
            <span class="icon-loading-small inlineblock"></span>
            <?php p($l->t("Saving changes ..."))?>
        </span>
    </div>
    <div id="cas-clients-loading">
        <span class="icon-loading-small inlineblock"></span>
        <?php p($l->t("Loading list of CAS clients ..."))?>
    </div>
</div>
