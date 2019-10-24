<?php


namespace OCA\cas\Migration;


use Doctrine\DBAL\Types\Type;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IMigrationStep;
use OCP\Migration\IOutput;

class Version001Initial implements IMigrationStep {

    /**
     * Human readable name of the migration step
     *
     * @return string
     * @since 14.0.0
     */
    public function name(): string {
        return "Initial creation of tables";
    }

    /**
     * Human readable description of the migration steps
     *
     * @return string
     * @since 14.0.0
     */
    public function description(): string {
        return "Initial creation of tables";
    }

    /**
     * @param IOutput $output
     * @param \Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @since 13.0.0
     */
    public function preSchemaChange(IOutput $output, \Closure $schemaClosure, array $options) {
    }

    /**
     * @param IOutput $output
     * @param \Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     * @since 13.0.0
     */
    public function changeSchema(IOutput $output, \Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();
        $ticketTable = $schema->createTable("cas_ticket");
        $ticketTable->addColumn("ticket", Type::STRING, [
            "notnull" => true,
            "length" => 100
        ]);
        $ticketTable->addColumn("created", Type::DATETIME, [
            "notnull" => true
        ]);
        $ticketTable->addColumn("expiry", Type::DATETIME, [
            "notnull" => true
        ]);
        $ticketTable->addColumn("service", Type::STRING, [
            "length" => 4096
        ]);
        $ticketTable->addColumn("renew", Type::SMALLINT, []);
        $ticketTable->addColumn("uid", Type::STRING, [
            "notnull" => true,
            "length" => 255
        ]);
        $ticketTable->setPrimaryKey(["ticket"]);
        $ticketTable->addIndex(["expiry"]);

        return $schema;
    }

    /**
     * @param IOutput $output
     * @param \Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @since 13.0.0
     */
    public function postSchemaChange(IOutput $output, \Closure $schemaClosure, array $options) {
    }
}