<?php
namespace OCA\cas\Controller;

use OCA\cas\Service\SettingsService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class AdminController extends Controller {
	private $userId;
    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct($AppName, IRequest $request, $UserId, SettingsService $settingsService) {
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
        $this->settingsService = $settingsService;
    }

    /**
     * @NoCSRFRequired
     */
    public function get() {
        return new JSONResponse([
            "services" => $this->settingsService->getServices(),
            "groups" => $this->settingsService->getGroups()
        ]);
    }

    public function post() {
        $this->settingsService->setServices($this->request->post["services"]);
        return new JSONResponse();
    }

}
