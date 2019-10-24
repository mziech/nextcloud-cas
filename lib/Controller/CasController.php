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

namespace OCA\cas\Controller;

use OC\AppFramework\Http;
use OCA\cas\Service\CasException;
use OCA\cas\Service\TicketService;
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
	 * CAUTION: the @Stuff turns off security checks; for this page no admin is
	 *          required and no CSRF check. If you don't know what CSRF is, read
	 *          it up in the docs or you might create a security hole. This is
	 *          basically the only required method to add this exemption, don't
	 *          add it to any other method if you don't exactly know what it does
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function login($service, $method = "GET") {
	    $model = [];
	    try {
            $model["ticket"] = $this->ticketService->createServiceTicket($service);
            $model["service"] = $service;
            $model["method"] = $method;

            //return new RedirectResponse($service . (strpos($service, "?") === FALSE ? '?' : '&')
            //    . "ticket=" . $ticket->getTicket());
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
            $this->logger->info("Rejecting CAS ticket validate due to: " . $e->getMessage(), [ "appName" => $this->appName ]);
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
