<?php


namespace Potatoe\Errors;


use Exception;
use JsonSerializable;
use Throwable;
use function gettext;
use function getenv;

class BaseError extends Exception implements JsonSerializable
{

    /**
     * @var bool
     */
    protected $autotranslate = false;

    public function __construct($message = "", bool $autotranslate = false, $code = 0, Throwable $previous = null)
    {
        error_log($message);
        $this->autotranslate = getenv('TRANSLATE') || $autotranslate;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function __toString()
    {

        return $this->autotranslate ? gettext(parent::getMessage()) : parent::getMessage();
    }


    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->__toString();
    }
}