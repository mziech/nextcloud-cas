<?php
/**
 * @copyright Copyright (c) 2022 Marco Ziech <marco+nc@ziech.net>
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

namespace OCA\Cas\Migration;


use OCP\DB\ISchemaWrapper;
use OCP\Migration\IMigrationStep;
use OCP\Migration\IOutput;

class Version003LengthFix implements IMigrationStep {

    /**
     * Human readable name of the migration step
     *
     * @return string
     * @since 14.0.0
     */
    public function name(): string {
        return "Add ID, rename expiry index";
    }

    /**
     * Human readable description of the migration steps
     *
     * @return string
     * @since 14.0.0
     */
    public function description(): string {
        return "Add surrogate ID to satisfy QBMapper and ensure expiry index has a defined name";
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
        $ticketTable = $schema->getTable("cas_ticket");

        $ticketTable->changeColumn("service", [
            "length" => 2000
        ]);

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
