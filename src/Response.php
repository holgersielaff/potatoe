<?php


namespace Potatoe;


class Response
{
    private static $_instance = null;

    /**
     * @return Response
     */
    public static function factory(): Response
    {
        if (self::$_instance === null) {
            self::$_instance = new self();

        }

        return self::$_instance;
    }

    /**
     * Response constructor.
     */
    private function __construct()
    {
    }

    /**
     * @var string
     */
    protected $content = '';
    /**
     * @var int
     */
    protected $status = 200;
    /**
     * @var array
     */
    protected $headers = [];
    /**
     * @var string
     */
    protected $application_type = 'text/html';

    /**
     * @param string $content
     *
     * @return Response
     */
    public function setContent(string $content): Response
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param mixed $content
     * @param int   $json_options
     *
     * @return Response
     */
    public function setJsonContent($content, int $json_options = JSON_NUMERIC_CHECK): Response
    {
        return $this
            ->setApplicationType('application/json')
            ->setContent(json_encode($content, $json_options));
    }

    /**
     * @param string $application_type
     *
     * @return Response
     */
    public function setApplicationType(string $application_type): Response
    {
        $this->application_type = $application_type;

        return $this;
    }

    /**
     * @param int $status
     *
     * @return Response
     */
    public function setStatusCode(int $status): Response
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return Response
     */
    public function setHeader(string $name, string $value): Response
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @return Response
     */
    public function send(): Response
    {
        if ($this->headers) {
            foreach ($this->headers as $k => $v) {
                header("$k:$v", true);
            }
        }
        header("Content-Type: {$this->application_type}", true);
        http_response_code($this->status);
        echo $this->content;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param bool $with_content
     */
    public function send404(bool $with_content = false)
    {
        header('HTTP/1.0 404 Not Found', true, 404);
        if ($with_content) {
            echo $this->content;
        }
    }

    /**
     * @param string $url
     */
    public function redirect(string $url)
    {
        header("Location: $url");
        exit(0);
    }

}