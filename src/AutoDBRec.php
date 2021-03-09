<?php


namespace Potatoe;

use PDO;
use PDOStatement;
use Exception;

class AutoDBRec
{
    /**#
     * @var AutoDBRec
     */
    private static $_instance = null;
    /**
     * @var PDO
     */
    private $dbh;

    final protected static function factory(): AutoDBRec
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * AutoDBRec constructor.
     *
     * @throws Exception
     */
    final private function __construct()
    {
        $this->onConstruct();
    }

    /**
     * @param ConfigInterface|null $config
     *
     * @throws Exception
     */
    public function onConstruct(ConfigInterface $config = null)
    {
        $config = $config ?? FactoryDefault::Config();
        $dns = sprintf(
            '%s:host=%s;port=%s;dbname=%s',
            $config->get('DB_TYPE', 'postgresql'),
            $config->get('DB_HOST', 'localhost'),
            $config->get('DB_PORT', 5432),
            $config->get('DB_NAME')
        );
        $this->dbh = new PDO(
            $dns,
            $config->get('DB_USER'),
            $config->get('DB_PASS'),
            [
                PDO::ATTR_PERSISTENT => false
            ]
        );
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }


    /**
     * @param       $query
     * @param array $args
     *
     * @return bool|PDOStatement
     */
    public function prepare($query, array $args = []): ?PDOStatement
    {
        $stmt = $this->dbh->prepare($query);
        if ($args !== []) {
            for ($i = 1; $i <= count($args); $i++) {
                $datatype = null;
                $value = $args[$i - 1];
                if (is_array($value)) {
                    list($value, $datatype) = $value;
                }
                if ($datatype === null) {
                    if (is_null($value)) {
                        $datatype = PDO::PARAM_NULL;
                    } elseif (is_int($value) || preg_match('!^[1-9][0-9]*$!', $value)) {
                        $datatype = PDO::PARAM_INT;
                    } else {
                        $datatype = PDO::PARAM_STR;
                    }
                }
                $stmt->bindValue($i, $value, $datatype);
            }
        }

        return $stmt;
    }

    /**
     * @param PDOStatement $stmt
     *
     * @return PDOStatement
     */
    public function execute(PDOStatement $stmt): PDOStatement
    {
        $stmt->execute();

        return $stmt;
    }

    /**
     * @param string $query
     * @param array  $args
     *
     * @return null|object
     */
    public static function newRec(string $query, ...$args)
    {
        $instance = self::factory();
        $stmt = $instance->prepare($query, $args);

        if (preg_match('!^ *s(elect|how) !i', $query)) {
            return $instance->execute($stmt)->fetchObject() ?: null;

        }
        $instance->execute($stmt);

        return null;

    }

    /**
     * @param string $query
     * @param mixed  ...$args
     *
     * @return array
     */

    public static function newArray(string $query, ...$args): array
    {
        $instance = self::factory();
        $stmt = $instance->prepare($query, $args);

        return $instance->execute($stmt)->fetchAll(PDO::FETCH_OBJ) ?: [];

    }

    /**
     * @param PDOStatement $stmt
     * @param              $class_or_object
     *
     * @return PDOStatement
     */
    public function setFetchMode(PDOStatement $stmt, $class_or_object): PDOStatement
    {
        $fetch_mode = is_object($class_or_object) ? PDO::FETCH_INTO : PDO::FETCH_CLASS;
        $stmt->setFetchMode($fetch_mode, $class_or_object);

        return $stmt;

    }

    /**
     * @param        $class_or_object
     * @param string $query
     * @param mixed  ...$args
     *
     * @return mixed
     */
    public static function newObjectRec($class_or_object, string $query, ...$args)
    {
        $instance = self::factory();
        $stmt = $instance->prepare($query, $args);
        $instance->execute($stmt);
        $instance->setFetchMode($stmt, $class_or_object);

        return $stmt->fetch() ?: null;
    }

    /**
     * @param        $class_or_object
     * @param string $query
     * @param mixed  ...$args
     *
     * @return array
     */
    public static function newObjectArray($class_or_object, string $query, ...$args): array
    {
        $instance = self::factory();
        $stmt = $instance->prepare($query, $args);
        $instance->setFetchMode($stmt, $class_or_object)->execute();

        return $stmt->fetchAll() ?: [];
    }

    /**
     * @return int
     */
    public static function lastInsertId()
    {
        return self::newRec('SELECT LAST_INSERT_ID() as id')->id;
    }

}
