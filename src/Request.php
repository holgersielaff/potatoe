<?php


namespace Potatoe;


use Potatoe\Errors\BaseError;
use FilterInterface;

class Request
{
    private static $_instance = null;

    /**
     * @return Response
     */
    public static function factory(): Request
    {
        if (self::$_instance === null) {
            self::$_instance = new self();

        }

        return self::$_instance;
    }

    private function __construct()
    {
        $this->sendCors();
    }

    /**
     * @return array
     * @throws \Potatoe\Errors\BaseError
     */
    public function getUrlParts(): array
    {
        $url = $this->getQuery('_url', FILTER_SANITIZE_STRING, '');

        return explode('/', $url);
    }

    /**
     * @return bool
     */
    public function isAjax(): bool
    {
        return isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] === "XMLHttpRequest";
    }

    /**
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->getMethod() === 'DELETE';
    }

    /**
     * @return bool
     */
    public function isPatch(): bool
    {
        return $this->getMethod() === 'PATCH';
    }

    /**
     * @return bool
     */
    public function isPut(): bool
    {
        return $this->getMethod() === 'PUT';
    }

    /**
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * @param string|null $key
     * @param int|null    $filter
     * @param null        $default
     *
     * @return mixed|null
     * @throws \Potatoe\Errors\BaseError
     */
    public function getPut(string $key = null, $filter = null, $default = null)
    {
        return $this->_getRequest('POST', $key, $filter, $default);
    }

    /**
     * @param string|null $key
     * @param int|null    $filter
     * @param null        $default
     *
     * @return mixed|null
     * @throws \Potatoe\Errors\BaseError
     */
    public function getPatch(string $key = null, $filter = null, $default = null)
    {
        return $this->_getRequest('POST', $key, $filter, $default);
    }

    /**
     * @param string|null $key
     * @param int|null    $filter
     * @param null        $default
     *
     * @return mixed|null
     * @throws \Potatoe\Errors\BaseError
     */
    public function getDelete(string $key = null, $filter = null, $default = null)
    {
        return $this->_getRequest('POST', $key, $filter, $default);
    }

    /**
     * @param string|null $key
     * @param int|null    $filter
     * @param null        $default
     *
     * @return mixed|null
     * @throws \Potatoe\Errors\BaseError
     */
    public function getQuery(string $key = null, $filter = null, $default = null)
    {
        return $this->_getRequest('GET', $key, $filter, $default);
    }

    /**
     * @param string|null $key
     * @param int|null    $filter
     * @param null        $default
     *
     * @return mixed|null
     * @throws \Potatoe\Errors\BaseError
     */
    public function getPost(string $key = null, $filter = null, $default = null)
    {
        return $this->_getRequest('POST', $key, $filter, $default);
    }


    /**
     * @param string      $method
     * @param string|null $key
     * @param int|null    $filter
     * @param null        $default
     *
     * @return mixed|null
     * @throws \Potatoe\Errors\BaseError
     */
    public function _getRequest(string $method, string $key = null, $filter = null, $default = null)
    {
        $method = strtoupper($method) === 'POST' ? $_POST : $_GET;
        if ( ! $key) {
            return $method;
        }
        $value = $method[$key] ?? null;
        if ($value) {
            if ($filter) {
                if ($filter instanceof FilterInterface) {
                    $value = $filter->setData($value)->execute()->getData();
                } elseif (is_int($filter)) {
                    $value = filter_var($value, $filter);
                } else {
                    throw new BaseError("Filter must be INT or instanceof FilterInterface");
                }
            }
        }

        return $value ? $value : $default;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        $returnMethod = "GET";

        $server = $_SERVER;

        if (isset($server["REQUEST_METHOD"])) {
            $returnMethod = strtoupper($_SERVER['REQUEST_METHOD']);
        }

        if ("POST" === $returnMethod) {
            $overridedMethod = $_SERVER["X-HTTP-METHOD-OVERRIDE"];

            if ( ! empty($overridedMethod)) {
                $returnMethod = strtoupper($overridedMethod);
            }
        }

        return $returnMethod;
    }

    /**
     * Sends Cors Header on OPTION Request
     */
    public function sendCors()
    {

        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        } else {
            header("Access-Control-Allow-Origin: *");

        }
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PATCH, PUT, HEAD");
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }

            exit(0);
        }
    }

}