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

use OCA\cas\Domain\Ticket;
use OCA\cas\Domain\TicketMapper;
use OCP\IGroup;
use OCP\IGroupManager;
use OCP\IUser;
use OCP\IUserManager;
use OCP\IUserSession;

class TicketService {

    /**
     * @var SettingsService
     */
    private $settingsService;
    /**
     * @var TicketMapper
     */
    private $ticketMapper;
    /**
     * @var IGroupManager
     */
    private $groupManager;
    /**
     * @var IUserManager
     */
    private $userManager;
    /**
     * @var IUserSession
     */
    private $userSession;

    public function __construct(IUserManager $userManager, IGroupManager $groupManager, IUserSession $userSession, SettingsService $settingsService, TicketMapper $ticketMapper) {
        $this->settingsService = $settingsService;
        $this->ticketMapper = $ticketMapper;
        $this->groupManager = $groupManager;
        $this->userManager = $userManager;
        $this->userSession = $userSession;
    }

    public function createServiceTicket(string $service, $renew = false) : Ticket {
        $s = $this->settingsService->getService($service);
        if ($s === null) {
            throw new CasException("Cannot find CAS service for URL: $service", "INVALID_SERVICE");
        }

        $user = $this->userSession->getUser();
        if (!empty($s["groups"])) {
            $groups = $this->groupManager->getUserGroupIds($user);
            if (empty(array_intersect($s['groups'], $groups))) {
                throw new CasException("User {$user->getUID()} is not allowed for service URL: $service", "FORBIDDEN");
            }
        }

        $ticket = new Ticket();
        $ticket->setService($service);
        $ticket->setTicket("ST-" . bin2hex(random_bytes(32)));
        $ticket->setCreated((new \DateTime())->format("Y-m-d H:i:s"));
        $expiry = new \DateTime();
        $expiry->add(new \DateInterval("PT10M"));
        $ticket->setExpiry($expiry->format("Y-m-d H:i:s"));
        $ticket->setUid($user->getUID());
        $ticket->setRenew((int) $renew);

        $this->ticketMapper->insert($ticket);
        return $ticket;
    }

    public function getAttributes(string $ticket, string $service, bool $includeAttributes, bool $renew = false) : array {
        /** @var Ticket $entity */
        $entity = $this->ticketMapper->findServiceTicket($ticket, $service);
        if ($entity === null) {
            throw new CasException("Cannot find CAS ticket $ticket for service URL: $service", "INVALID_TICKET");
        }

        if ($renew && !$entity->getRenew()) {
            throw new CasException("CAS ticket $ticket for service URL '$service' is not a renew ticket'", "INVALID_TICKET");
        }

        $s = $this->settingsService->getService($service);
        if ($s === null) {
            throw new CasException("Cannot find CAS service for URL: $service", "INVALID_SERVICE");
        }

        /** @var IUser $user */
        $user = $this->userManager->get($entity->getUid());

        $response = [
            "authenticationSuccess" => [
                "user" => $entity->getUid()
            ]
        ];

        if ($includeAttributes || !isset($s['strict']) || !$s['strict']) {
            $response["authenticationSuccess"]["attributes"] = [
                "displayName" => $user->getDisplayName(),
                "email" => $user->getEMailAddress(),
                "memberOf" => $this->getMemberOf($user)
            ];
        }

        return $response;
    }

    private function getMemberOf(IUser $user) {
        $groups = $this->groupManager->getUserGroups($user);
        return array_values(array_map(function (IGroup $group) {
            return $group->getGID();
        }, $groups));
    }

}