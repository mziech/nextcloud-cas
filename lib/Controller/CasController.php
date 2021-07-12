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

use OC\AppFramework\Http;
use OCA\Cas\Service\CasException;
use OCA\Cas\Service\TicketService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataDisplayResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\ILogger;
use OCP\IRequest;

class CasController extends Controller {
    private $userId;
    /**
     * @var TicketService
     */
    private $ticketService;
    /**
     * @var ILogger
     */
    private $logger;

    public function __construct($AppName, IRequest $request, $UserId, TicketService $ticketService, ILogger $logger) {
        parent::__construct($AppName, $request);
        $this->userId = $UserId;
        $this->ticketService = $ticketService;
        $this->logger = $logger;
    }

    /**
     * @param string $service
     * @param string $method
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function login($service, $method = "GET") {
        $model = [];
        try {
            if ($service === null) {
                throw new CasException("No CAS service specified", "INVALID_SERVICE");
            }

            $model["ticket"] = $this->ticketService->createServiceTicket($service);
            $model["service"] = $service;
            $model["method"] = $method;
        } catch (CasException $exception) {
            $model["errorCode"] = $exception->getCasCode();
        }

        return new TemplateResponse($this->appName, "login", $model, "guest");
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function validate($service, $ticket, $renew = false) {
        $text = "no\n\n";
        try {
            $attrs = $this->ticketService->getAttributes($ticket, $service, $renew);
            $text = "yes\n" . $attrs["serviceResponse"]["authenticationSuccess"]["user"] . "\n";
        } catch (\Exception $e) {
            $this->logger->info("Rejecting CAS ticket validate due to: " . $e->getMessage(), ["appName" => $this->appName]);
        }

        return new DataDisplayResponse(
            $text,
            Http::STATUS_OK,
            ['Content-Type' => 'text/plain; charset="utf-8"']
        );
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function serviceValidateV2($service, $ticket, $format = "XML", $renew = false) {
        return $this->serviceValidate($service, $ticket, $format, $renew, false);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function serviceValidateV3($service, $ticket, $format = "XML", $renew = false) {
        return $this->serviceValidate($service, $ticket, $format, $renew, true);
    }

    /**
     * Same as #serviceValidateV2 but would ALSO validate proxy tickets, which are not supported at the moment.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function proxyValidateV2($service, $ticket, $format = "XML", $renew = false) {
        return $this->serviceValidate($service, $ticket, $format, $renew, false);
    }

    /**
     * Same as #serviceValidateV3 but would ALSO validate proxy tickets, which are not supported at the moment.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function proxyValidateV3($service, $ticket, $format = "XML", $renew = false) {
        return $this->serviceValidate($service, $ticket, $format, $renew, true);
    }

    private function serviceValidate($service, $ticket, $format, $renew, $includeAttributes) {
        try {
            $this->requireParam($service, "service");
            $this->requireParam($ticket, "ticket");

            $json = $this->ticketService->getAttributes($ticket, $service, $includeAttributes, $renew);
            return $this->serviceValidateResponse($format, $json);
        } catch (CasException $e) {
            return $this->serviceValidateResponse($format, [
                "authenticationFailure" => [
                    "code" => $e->getCasCode(),
                    "description" => $e->getMessage()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->serviceValidateResponse($format, [
                "authenticationFailure" => [
                    "code" => "INTERNAL_ERROR",
                    "description" => $e->getMessage()
                ]
            ]);
        }
    }

    private function serviceValidateResponse($format, $json) {
        if ($format === 'JSON') {
            return new JSONResponse($json);
        }

        return new CasXmlResponse($json);
    }

    private function requireParam($value, $param) {
        if ($value !== null) {
            return;
        }

        throw new CasException("Parameter '$param' is required", "INVALID_REQUEST");
    }

}
