<?php


namespace Potatoe;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class Twig extends Environment implements TwigInterface
{
    /**
     * @var Twig
     */
    protected static $_instance = null;
    /**
     * @var array
     */
    public static $tempatepaths = [BASEPATH . '/templates'];
    /**
     * @var array
     */
    protected $_templatedata = [];


    /**
     * @return Twig
     */
    public static function factory(): TwigInterface
    {
        if (static::$_instance === null) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    /**
     * Twig constructor.
     *
     * @param array|null $options
     */
    public function __construct(?array $options = [])
    {
        if (self::$_instance !== null) {
            return;
        }
        $options = $options ?? [];
        if ( ! isset($options['cache'])) {
            $options['cache'] = '/tmp/twigcache';
        }
        if ( ! is_dir($options['cache'])) {
            mkdir($options['cache']);
        }
        parent::__construct(new FilesystemLoader(self::$tempatepaths), $options);
        $this->enableAutoReload();
    }

    /**
     * @param string $path
     *
     * @return Twig
     */
    public function appendTemplatePath(string $path): TwigInterface
    {
        $this->getLoader()->addPath($path);

        return $this;
    }

    /**
     * @param string $path
     *
     * @return Twig
     */
    public function prependTemplatePath(string $path): TwigInterface
    {
        $this->getLoader()->prependPath($path);

        return $this;

    }

    /**
     * @param string $templatefile
     * @param array  $data
     * @param int    $statuscode
     * @param string $application_type
     * @param array  $headers
     */
    public function renderAndSend(string $templatefile, ?array $data = [], int $statuscode = 200, string $application_type = 'text/html', array $headers = [])
    {
        $response = $this->response();
        if ($headers) {
            foreach ($headers as $headername => $headervalue) {
                $response->setHeader($headername, $headervalue);
            }
        }
        $response
            ->setStatusCode($statuscode)
            ->setApplicationType($application_type)
            ->setContent($this->render($templatefile, $data))
            ->send();
    }

    /**
     * @param string $template
     * @param array  $context
     *
     * @return string
     */
    public function render($template, array $context = [])
    {
        Timer::begin("prepare $template");
        $this
            ->setTemplateData('session', $this->session())
            ->setTemplateData('globals', $this->globals());
        if ($context) {
            foreach ($context as $k => $v) {
                $this->setTemplateData($k, $v);
            }
        }
        Timer::end("prepare $template");
        Timer::begin("render $template");
        $return = parent::render($template, $this->getTemplateData());
        Timer::end("render $template");

        return $return;

    }

    /**
     * @return Request
     */
    public function request(): Request
    {
        return FactoryDefault::Request();
    }

    /**
     * @return Response
     */
    public function response(): Response
    {
        return FactoryDefault::Response();
    }

    /**
     * @return Session
     */
    public function session(): SessionInterface
    {
        return FactoryDefault::Session();
    }

    /**
     * @return Globals
     */
    public function globals(): GlobalsInterface
    {
        return FactoryDefault::Globals();
    }

    /**
     * @param string $key
     * @param        $value
     *
     * @return TwigInterface
     */
    public function setTemplateData(string $key, $value): TwigInterface
    {
        $this->_templatedata[$key] = $value;

        return $this;

    }

    /**
     * @return array
     */
    public function getTemplateData(): array
    {
        return $this->_templatedata;
    }

}