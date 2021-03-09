<?php


namespace Potatoe;


use Exception;

trait ErrorAwareTrait
{
    /**
     * @var array
     */
    public $errors = [];

    /**
     * @param bool $translate
     *
     * @return array
     */
    public function getErrors(bool $translate = false): array
    {
        if ($translate || getenv('TRANSLATE')) {
            $errors = array_map(function ($a) {
                return array_map('gettext', $a);
            }, $this->errors);
        } else {
            $errors = $this->errors;
        }

        return $errors;
    }

    /**
     * @return bool
     */
    public function hasError(): bool
    {
        return $this->errors !== [];
    }

    /**
     * @param string|Exception $errormessage
     * @param string|null      $errorkey
     *
     * @return ErrorAwareInterface
     */
    public function setError($errormessage, ?string $errorkey = null): ErrorAwareInterface
    {
        if ($errormessage instanceof Exception) {
            $errormessage = $errormessage->getMessage();
        }
        if ($errorkey !== null) {
            if ( ! isset($this->errors[$errorkey])) {
                $this->errors[$errorkey] = [];
            }
            $this->errors[$errorkey][] = $errormessage;
        } else {
            $this->errors[] = $errormessage;
        }

        return $this;
    }

}
