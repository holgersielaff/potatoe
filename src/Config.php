<?php


namespace Potatoe;


use Exception;
use function getenv;
use const PATHINFO_EXTENSION;

class Config implements ConfigInterface
{
    public const FILE_TYPE_YAML = 'yaml';
    public const FILE_TYPE_JSON = 'json';
    public const FILE_TYPE_CONF = 'conf';

    /**
     * @var Config
     */
    protected static $_instance = null;

    /**
     * @var array
     */
    public $cfg = [];

    /**
     * @var string
     */
    protected $_configfile = null;

    /**
     * @param string|null $config_file
     *
     * @return ConfigInterface
     * @throws Exception
     */
    public static function factory(string $config_file = null): ConfigInterface
    {
        if (static::$_instance === null) {
            static::$_instance = new static($config_file);
        }

        return static::$_instance;

    }

    /**
     * Config constructor.
     *
     * @param string|null $config_file
     *
     * @throws Exception
     */
    protected function __construct(string $config_file = null)
    {
        if ($config_file !== null) {
            $this->_configfile = $config_file;
            switch ($this->config_file_type()) {
                case self::FILE_TYPE_CONF:
                    $this->cfg = parse_ini_file($this->_configfile);
                    break;
                case self::FILE_TYPE_JSON:
                    $this->cfg = json_decode($this->_configfile);
                    break;
                case self::FILE_TYPE_YAML:
                    $this->cfg = yaml_parse_file($this->_configfile);
                    break;
            }
        }
    }

    /**
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     * @throws Exception
     */
    public function get(string $key, $default = null)
    {
        $value = null;
        if (($value = getenv($key)) === false) {
            if (isset($this->cfg[$key])) {
                return $this->cfg[$key];
            }
        }
        if ($value === null && $default === null) {
            throw new Exception("{$key} not defined in any config - no default param set");
        }

        return $value ?: $default;
    }


    /**
     * @return string|null
     * @throws Exception
     */
    protected function config_file_type(): ?string
    {
        if ($this->_configfile === null) {
            return null;
        }
        switch (strtolower(pathinfo($this->_configfile, PATHINFO_EXTENSION))) {
            case 'yml':
            case 'yaml':
                return self::FILE_TYPE_YAML;
            case 'json':
                return self::FILE_TYPE_JSON;
            case 'conf':
            case 'cnf':
                return self::FILE_TYPE_CONF;
            default:
                if (basename($this->_configfile) !== '.env') {
                    throw new Exception('Configfile must be *.y(a)ml, *.json, *.c(o)nf or */.env');
                }

                return self::FILE_TYPE_CONF;
        }

    }


}