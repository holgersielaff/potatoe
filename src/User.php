<?php


namespace Potatoe;


class User implements UserInterface
{
    /**
     * @var User
     */
    protected static $_instance = null;

    /**
     * @var bool
     */
    protected $_is_logged_in = false;

    /**
     * @return User
     */
    public static function factory(): UserInterface
    {
        if (static::$_instance === null) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $group;

    /**
     * @param bool $loggedin
     *
     * @return UserInterface
     */
    public function setLoggedIn(bool $loggedin = false): UserInterface
    {
        $this->_is_logged_in = $loggedin;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->_is_logged_in;
    }

    /**
     * @param UserInterface $user
     */
    public function setInstance(UserInterface $user)
    {
        foreach ($user as $k => $v) {
            $this->{$k} = $v;
        }
    }

    /**
     * @return UserInterface|null
     */
    public static function getInstance(): ?UserInterface
    {
        return static::$_instance;
    }


}