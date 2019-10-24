<?php


namespace OCA\cas\Domain;


use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
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

    public function deleteOldTickets() {
        $qb = $this->db->getQueryBuilder();
        $qb->delete()
            ->from("cas_ticket")
            ->where(
                $qb->expr()->lt('created', $qb->createNamedParameter((new \DateTime())->sub(new \DateInterval("PT7D")), IQueryBuilder::PARAM_DATE)),
                $qb->expr()->lt('expiry', $qb->createNamedParameter(new \DateTime(), IQueryBuilder::PARAM_DATE))
            );
    }

}