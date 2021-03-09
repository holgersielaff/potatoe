<?php


namespace Potatoe;


class Timer
{
    /**
     * @var Timer
     */
    private static $_instance = null;
    /**
     * @var array
     */
    public $breakpoints = [];
    private $_actual_breakpoint = null;

    /**
     * @return Timer
     */
    public static function factory(): Timer
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Timer constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param string $name
     *
     * @return Timer
     */
    public static function begin(string $name): Timer
    {
        $i = self::factory();
        $i->breakpoints[$name] = ['start' => microtime(true), 'sum' => 0, 'end' => 0];
        $i->_actual_breakpoint = $name;

        return $i;
    }

    /**
     * @param string|null $name
     *
     * @return Timer
     */
    public static function end(?string $name = null): Timer
    {
        $i = self::factory();
        $name = $name ?? $i->_actual_breakpoint;
        $i->breakpoints[$name]['end'] = microtime(true);

        return $i;
    }

    /**
     * @return Timer
     */
    public function calc(): Timer
    {
        foreach ($this->breakpoints as $k => &$v) {
            $v['sum'] = $v['end'] - $v['start'];
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $ret = array_combine(array_keys($this->breakpoints), array_column($this->calc()->breakpoints, 'sum'));

        return "<pre>" . print_r($ret, true) . "</pre>";
    }


}