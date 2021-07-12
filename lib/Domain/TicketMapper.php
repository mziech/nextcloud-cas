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

namespace OCA\Cas\Domain;


use DateInterval;
use DateTimeImmutable;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class TicketMapper extends QBMapper {

    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'cas_ticket');
    }

    public function findServiceTicket($ticket, $service) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from('cas_ticket')
            ->where(
                $qb->expr()->eq('ticket', $qb->createNamedParameter($ticket, IQueryBuilder::PARAM_STR)),
                $qb->expr()->eq('service', $qb->createNamedParameter($service, IQueryBuilder::PARAM_STR)),
                $qb->expr()->gt('expiry', $qb->createNamedParameter(new \DateTime(), IQueryBuilder::PARAM_DATE))
            );

        try {
            return $this->findEntity($qb);
        } catch (DoesNotExistException $e) {
            return null;
        }
    }

    public function deleteOldTickets(DateTimeImmutable $maxCreated, DateTimeImmutable $maxExpiry) {
        $qb = $this->db->getQueryBuilder();
        $qb->delete("cas_ticket")
            ->where(
                $qb->expr()->lt('created', $qb->createNamedParameter($maxCreated, IQueryBuilder::PARAM_DATE)),
                $qb->expr()->lt('expiry', $qb->createNamedParameter($maxExpiry, IQueryBuilder::PARAM_DATE))
            )->execute();
    }

}