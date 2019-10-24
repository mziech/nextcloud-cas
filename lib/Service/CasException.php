<?php


namespace OCA\cas\Service;


use Throwable;

class CasException extends \Exception {

    /**
     * @var string
     */
    private $casCode;

    public function __construct($message = "", $casCode = "INTERNAL_ERROR", Throwable $previous = null) {
        parent::__construct($message, 0, $previous);
        $this->casCode = $casCode;
    }

    /**
     * @return string
     */
    public function getCasCode(): string {
        return $this->casCode;
    }

}
