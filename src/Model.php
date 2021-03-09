<?php


namespace Potatoe;

use JsonSerializable;
use stdClass;

abstract class Model implements JsonSerializable
{


    protected static $_source;

    /**
     * @return string
     */
    abstract public function setSource(): string;

    /**
     * @return mixed
     */
    public function preSave()
    {

    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return static::$_source;
    }

    /**
     * Model constructor.
     */
    final public function __construct()
    {
        $this->initialize();
        $this->onConstruct();
    }

    /**
     *
     */
    protected function initialize()
    {
        static::$_source = $this->setSource();
    }

    /**
     *
     */
    protected function onConstruct()
    {

    }

    /**
     * @return $this
     */
    public function delete()
    {
        AutoDBRec::newRec(sprintf("DELETE FROM %s WHERE id = ?", static::$_source), $this->id);
        $this->id = null;

        return $this;
    }

    /**
     * @param array $values
     *
     * @return Model
     */
    public static function create(array $values)
    {
        return (new static())->save($values);
    }

    /**
     *
     */
    protected function updateAfterCreate()
    {
        $this->id = (int)AutoDBRec::lastInsertId();
    }

    /**
     * @param array $values
     *
     * @return Model
     */
    public function update(array $values = [])
    {
        return $this->save($values);
    }

    /**
     * @param array|null $values
     *
     * @return $this
     */
    public function save(?array $values = [])
    {
        if ($values) {
            $this->assign($values);
        }
        $args = get_object_vars($this->preSave());
        unset($args['id']);
        $bindargs = array_values($args);
        $fields = array_keys($args);
        if ($this->id) {
            $bindargs[] = $this->id;
            $placeholder = array_map(function ($x) {
                return "{$x} = ?";
            }, $fields);
            $q = sprintf("UPDATE %s SET %s WHERE id = ?", static::$_source, join(', ', $placeholder));
        } else {
            $placeholder = array_fill(1, count($bindargs), '?');
            $q = sprintf("INSERT INTO %s (%s) VALUES(%s)", static::$_source, join(',', $fields), join(',', $placeholder));
        }
        array_unshift($bindargs, $q);
        call_user_func_array('\Potatoe\AutoDBRec::newRec', $bindargs);
        if ( ! $this->id) {
            $this->updateAfterCreate();
        }

        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function assign(array $data)
    {
        foreach (get_object_vars($this) as $prop => $val) {
            if (isset($data[$prop])) {
                $this->{$prop} = $data[$prop];
            }
        }

        return $this;
    }

    /**
     * @param array|null $args
     *
     * @return array
     */
    public static function find(?array $args = [])
    {
        $bindargs = [];
        $q = static::_querybuilder($args, $bindargs);
        array_unshift($bindargs, static::class, $q);

        return call_user_func_array('\Potatoe\AutoDBRec::newObjectArray', $bindargs);
    }

    /**
     * @param array|null $args
     *
     * @return mixed
     */
    public static function findOne(?array $args = [])
    {
        $args['limit'] = 1;
        unset($args['offset']);
        $bindargs = [];
        $q = static::_querybuilder($args, $bindargs);
        array_unshift($bindargs, static::class, $q);

        return call_user_func_array('\Potaoe\AutoDBRec::newObjectRec', $bindargs);
    }

    /**
     * @param array      $args
     * @param array|null $bindargs
     *
     * @return string
     */
    protected static function _querybuilder(array $args, ?array &$bindargs = []): string
    {
        $q = sprintf("SELECT * FROM %s ", static::$_source);
        if ($args) {
            if (array_key_exists('conditions', $args)) {
                $q .= "WHERE {$args['conditions']}";
                if (array_key_exists('bind', $args)) {
                    $bindargs = $args['bind'];
                }
            }
            if (array_key_exists('group', $args)) {
                $q .= ' GROUP BY ' . $args['group'];
            }
            if (array_key_exists('order', $args)) {
                $q .= ' ORDER BY ' . $args['order'];
            }
            $limit = [];
            if (array_key_exists('offset', $args)) {
                $limit[] = $args['offset'];
            }
            if (array_key_exists('limit', $args)) {
                $limit[] = $args['limit'];
            }
            if ($limit) {
                $q .= ' LIMIT ' . join(', ', $limit);
            }
        }

        return $q;
    }

    /**
     * @return mixed|stdClass
     */
    public function jsonSerialize()
    {
        $return = new stdClass();
        foreach (get_object_vars($this) as $k => $v) {
            $return->{$k} = $v;
        }

        return $return;
    }

    /**
     * @param $id
     *
     * @return Model
     */
    public static function getById($id)
    {
        return static::findOne([
            'conditions' => 'id = ?',
            'bind'       => [(int)$id],
        ]);
    }

    /**
     * Unsets this->id
     */
    function __clone()
    {
        $this->id = null;
    }

}