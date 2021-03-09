<?php


namespace Potatoe;


interface UserInterface
{
    /**
     * @return UserInterface
     */
    public static function factory(): UserInterface;

    /**
     * @param bool $loggedin
     *
     * @return UserInterface
     */
    public function setLoggedIn(bool $loggedin = false): UserInterface;

    /**
     * @return bool
     */
    public function isLoggedIn(): bool;

    /**
     * @param UserInterface $user
     *
     * @return mixed
     */
    public function setInstance(UserInterface $user);

    /**
     * @return UserInterface|null
     */
    public static function getInstance(): ?UserInterface;

}