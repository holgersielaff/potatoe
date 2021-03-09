<?php


namespace Potatoe;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Exception;


class FactoryDefault
{
    /**
     * @return Session
     */
    public static function Session(): SessionInterface
    {
        return Session::factory();
    }

    /**
     * @return User
     */
    public static function User(): ?UserInterface
    {
        return User::factory();
    }

    /**
     * @return Response
     */
    public static function Response(): Response
    {
        return Response::factory();
    }

    /**
     * @return Request
     */
    public static function Request(): Request
    {
        return Request::factory();
    }

    /**
     * @return Twig
     */
    public static function Twig(): TwigInterface
    {
        return Twig::factory();
    }

    /**
     * @return Globals
     */
    public static function Globals(): GlobalsInterface
    {
        return Globals::factory();
    }

    /**
     * @param string|null $config_file
     *
     * @return Config
     * @throws Exception
     */
    public static function Config(string $config_file = null): Config
    {
        return Config::factory($config_file);
    }


}