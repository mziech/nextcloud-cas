<?php
/**
 * @copyright Copyright (c) 2019 Marco Ziech <marco+nc@ziech.net>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/** @var $l \OCP\IL10N */
/** @var $_ array */

script('cas', 'settings');
style('cas', 'settings');

?>

<div id="cas" class="section">
    <h2><?php p($l->t("CAS Server"))?></h2>
    <p class="settings-hint">
        <?php p($l->t("Client should be configured to use the following CAS base URL: "))?><br/>
        <a href="<?php p($_["baseUrl"])?>"><?php p($_["baseUrl"])?></a>
    </p>
    <h3><?php p($l->t("Service Providers"))?></h3>
    <div id="cas-clients" class="hidden">
        <table class="grid">
            <thead>
            <tr>
                <th><?php p($l->t("ID"))?></th>
                <th><?php p($l->t("URL"))?></th>
                <th><?php p($l->t("URL match type"))?></th>
                <th><?php p($l->t("Groups"))?></th>
                <th><abbr title="<?php p($l->t("Strictly adhere to CAS specification, do not send attributes for 2.0 tickets."))?>"><?php p($l->t("Strict"))?></abbr></th>
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
