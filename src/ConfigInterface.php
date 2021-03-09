<?php


namespace Potatoe;


interface ConfigInterface
{
    /**
     * @param string $key
     * @param null   $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null);

}