<?php


namespace Potatoe;


use Potatoe\Errors\NotImplementedError;
use Exception;

class Session implements SessionInterface
{
    /**
     * @var Session
     */
    protected static $_instance = null;
    /**
     * @var UserInterface
     */
    public $user;

    /**
     * Session constructor.
     *
     * @throws Exception
     */
    protected function __construct()
    {
        session_start();
        if ( ! isset($_SESSION['_default'])) {
            $_SESSION['_default'] = $this;
        } else {
            foreach ($_SESSION['_default'] as $key => $value) {
                $this->{$key} = $value;
            }
        }
        try{
            $this->setUser();
        }catch (NotImplementedError $e){
            error_log('setUser() is not implemented!');
        }catch (Exception $e){
            error_log($e->getMessage());
            throw $e;
        }

    }

    /**
     * @param UserInterface $user
     *
     * Example:
     *
     * if ( ! User::getInstance() && $this->getUser()) {
     *     User::factory()->setInstance($this->getUser());
     * }
     *
     * @return SessionInterface
     * @throws NotImplementedError
     */
    public function setUser(?UserInterface $user = null): ?SessionInterface
    {
        throw new NotImplementedError();
    }

    /**
     * @return Session
     */
    public static function factory(): SessionInterface
    {
        if (static::$_instance === null) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    /**
     * @param string $key
     * @param        $value
     *
     * @return Session
     */
    public function setParam(string $key, $value): SessionInterface
    {
        $this->{$key} = $value;
        $_SESSION['_default'] = $this;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $_SESSION['_default'];
    }

    /**
     * @return User
     */
    public function getUser(): ?UserInterface
    {
        return $this->getDefault()->user;
    }

    /**
     *
     */
    public function logout()
    {
        unset($_SESSION['_default']);
        session_destroy();
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return bool
     * @throws NotImplementedError
     */
    public function login(string $username, string $password): bool
    {
        throw new NotImplementedError();
    }

}