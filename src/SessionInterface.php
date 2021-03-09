<?php


namespace Potatoe;


interface SessionInterface
{
    /**
     * @return SessionInterface
     */
    public static function factory(): SessionInterface;

    /**
     * @param string $key
     * @param        $param
     *
     * @return SessionInterface
     */
    public function setParam(string $key, $param): SessionInterface;

    /**
     * @return mixed
     */
    public function getDefault();

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface;

    /**
     * @param UserInterface|null $user
     *
     * @return SessionInterface|null
     */
    public function setUser(?UserInterface $user = null): ?SessionInterface;

    /**
     * @return mixed
     */
    public function logout();

    /**
     * @return bool
     */
    public function login(string $username, string $password): bool;

}