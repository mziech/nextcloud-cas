<?php


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
