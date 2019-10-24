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

namespace OCA\cas\Service;


use OCP\IConfig;
use OCP\IGroupManager;

class SettingsService {

    private $config;
    private $AppName;
    /**
     * @var IGroupManager
     */
    private $groupManager;

    /**
     * SettingsService constructor.
     * @param $config
     */
    public function __construct($AppName, IConfig $config, IGroupManager $groupManager) {
        $this->config = $config;
        $this->AppName = $AppName;
        $this->groupManager = $groupManager;
    }

    public function getService(string $service) {
        foreach ($this->getServices() as $item) {
            $url = $item['url'];
            switch ($item['urlMatchType']) {
                case 'EXACT':
                    if ($url === $service) {
                        return $item;
                    }
                    break;
                case 'PREFIX';
                    if (strpos($service, $url) === 0) {
                        return $item;
                    }
                    break;
                case 'REGEX';
                    if (preg_match($url, $service) === 0) {
                        return $item;
                    }
                    break;
            }
        }
        return null;
    }

    public function getServices() {
        return json_decode($this->config->getAppValue($this->AppName, "services", "[]"), TRUE);
    }

    public function setServices(array $services) {
        $this->config->setAppValue($this->AppName, "services", json_encode(array_values($services)));
    }

    public function getGroups() {
        $arr = [];
        foreach ($this->groupManager->search("") as $group) {
            $arr[$group->getGID()] = $group->getDisplayName();
        }
        return $arr;
    }

}
