<?php


namespace Potatoe;


interface ErrorAwareInterface
{
    /**
     * @return bool
     */
    public function hasError(): bool;

    /**
     * @param bool $translate
     *
     * @return array
     */
    public function getErrors(bool $translate = false): array;

    /**
     * @param string $errormessage
     *
     * @return ErrorAwareInterface
     */
    public function setError(string $errormessage): ErrorAwareInterface;

}