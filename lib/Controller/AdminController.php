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

namespace OCA\Cas\Controller;

use OCA\Cas\Service\SettingsService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IURLGenerator;

class AdminController extends Controller {
    /**
     * @var IURLGenerator
     */
    private $urlGenerator;

    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct($AppName, IRequest $request, IURLGenerator $urlGenerator, SettingsService $settingsService) {
		parent::__construct($AppName, $request);
        $this->urlGenerator = $urlGenerator;
        $this->settingsService = $settingsService;
    }

    /**
     * @NoCSRFRequired
     */
    public function get() {
        return new JSONResponse([
            "baseUrl" => $this->urlGenerator->getAbsoluteURL("/apps/{$this->appName}/"),
            "services" => $this->settingsService->getServices(),
            "groups" => $this->settingsService->getGroups()
        ]);
    }

    public function post() {
        $this->settingsService->setServices($this->request->post["services"]);
        return new JSONResponse();
    }

}
