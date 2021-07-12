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

use OCP\AppFramework\Http\Response;

class CasXmlResponse extends Response {
    private $xml;

    public function __construct(array $xml) {
        $this->addHeader('Content-Type', 'application/xml');
        $this->xml = $xml;
    }

    public function render() {
        $root = new \SimpleXMLElement("<cas:serviceResponse xmlns:cas=\"http://www.yale.edu/tp/cas\"></cas:serviceResponse>");
        $this->xmlNode($root, $this->xml);
        return $root->asXML();
    }

    private function xmlNode(\SimpleXMLElement $parent, $data) {
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                if (array_values($v) !== $v) {
                    // Associative array
                    $this->xmlNode($parent->addChild("cas:$k"), $v);
                } else {
                    // Indexed / sequential array
                    foreach ($v as $it) {
                        $parent->addChild("cas:$k", $it);
                    }
                }
            } else {
                $parent->addChild("cas:$k", $v);
            }
        }
    }
}