<?php


namespace OCA\cas\Controller;


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
                if (array_values($v) === $v) {
                    foreach ($v as $it) {
                        $parent->addChild("cas:$k", $it);
                    }
                } else {
                    $this->xmlNode($parent->addChild("cas:$k"), $v);
                }
            } else {
                $parent->addChild("cas:$k", $v);
            }
        }
    }
}