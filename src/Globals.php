<?php


namespace Potatoe;

class Globals implements GlobalsInterface
{

    /**
     * @var Globals
     */
    protected static $_instance = null;


    /**
     * Globals constructor.
     */
    protected function __construct()
    {
    }

    /**
     * @return GlobalsInterface
     */
    public static function factory(): GlobalsInterface
    {
        if (static::$_instance === null) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

}