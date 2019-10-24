<?php
declare(strict_types=1);
/**
 * @copyright Copyright (c) 2017 Lukas Reschke <lukas@statuscode.ch>
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

namespace OCA\cas\Settings;

use OCA\cas\Service\SettingsService;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;

class Admin implements ISettings {

    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(SettingsService $settingsService) {
        $this->settingsService = $settingsService;
    }

    public function getForm(): TemplateResponse {
		return new TemplateResponse(
			'cas',
			'admin',
			[ "services" =>  $this->settingsService->getServices() ],
			''
		);
	}

	public function getSection(): string {
		return 'security';
	}

	public function getPriority(): int {
		return 0;
	}
}
