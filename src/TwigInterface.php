<?php


namespace Potatoe;


interface TwigInterface
{
    /**
     * @return TwigInterface
     */
    public static function factory(): TwigInterface;

    /**
     * @param string $key
     * @param        $value
     *
     * @return TwigInterface
     */
    public function setTemplateData(string $key, $value): TwigInterface;

    /**
     * @return array
     */
    public function getTemplateData(): array;

    /**
     * TwigInterface constructor.
     *
     * @param array|null $options
     */
    public function __construct(?array $options = []);

    /**
     * @param string $path
     *
     * @return TwigInterface
     */
    public function appendTemplatePath(string $path): TwigInterface;

    /**
     * @param string $path
     *
     * @return TwigInterface
     */
    public function prependTemplatePath(string $path): TwigInterface;

    /**
     * @param string $templatefile
     * @param array  $data
     * @param int    $statuscode
     * @param string $application_type
     * @param array  $headers
     *
     * @return mixed
     */
    public function renderAndSend(string $templatefile, ?array $data = [], int $statuscode = 200, string $application_type = 'text/html', array $headers = []);

    /**
     * @return Request
     */
    public function request(): Request;

    /**
     * @return SessionInterface
     */
    public function session(): SessionInterface;

    /**
     * @return GlobalsInterface
     */
    public function globals(): GlobalsInterface;

}