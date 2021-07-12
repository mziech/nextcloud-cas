<?php
/**
 * @copyright Copyright (c) 2021 Marco Ziech <marco+nc@ziech.net>
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


namespace OCA\Cas\Job;



use DateInterval;
use DateTimeImmutable;
use Exception;
use OCA\Cas\Domain\TicketMapper;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\TimedJob;
use Psr\Log\LoggerInterface;

class TicketCleanupJob extends TimedJob {

    /**
     * @var TicketMapper
     */
    private $ticketMapper;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ITimeFactory $time, TicketMapper $ticketMapper, LoggerInterface $logger) {
        parent::__construct($time);

        // Run daily
        $this->setInterval(24 * 3600);
        $this->ticketMapper = $ticketMapper;
        $this->logger = $logger;
    }

    protected function run($argument) {
        try {
            $maxExpiry = new DateTimeImmutable();
            $retention = new DateInterval("P7D");
            $maxCreated = $maxExpiry->sub($retention);
            $this->logger->info("Deleting CAS tickets created before {$maxCreated->format("c")} and expired before {$maxExpiry->format("c")}");
            $this->ticketMapper->deleteOldTickets($maxCreated, $maxExpiry);
        } catch (Exception $e) {
            $this->logger->error("CAS ticket cleanup failed", ["exception" => $e]);
        }
    }

}