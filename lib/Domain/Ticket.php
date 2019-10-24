<?php

namespace OCA\cas\Domain;

use Doctrine\DBAL\Types\Type;
use OCP\AppFramework\Db\Entity;

class Ticket extends Entity {
    protected $ticket;
    protected $created;
    protected $expiry;
    protected $renew;
    protected $service;
    protected $uid;

    public function __construct() {
        $this->addType('renew', Type::INTEGER);
    }

}
