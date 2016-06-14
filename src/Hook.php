<?php
namespace leoding86\SimpleHook;

class Hook
{
    static private $hooks;

    static private function invoke($listener, array $args, $id)
    {
        if (is_string($listener) || is_callable($listener)) {
            call_user_func_array($listener, $args);
        }
        else if (
            is_array($listener) &&
            method_exists($listener[0], $listener[1])
        ) {
            call_user_func_array(array($listener[0], $listener[1]), $args);
        }
        else {
            throw new Exception("Invalid listener", $id);
        }
    }

    static public function addListener($name, $listener, $id = null)
    {
        if (!isset(self::$hooks[$name])) {
            self::$hooks[$name] = array();
        }

        if (is_string($id)) {
            self::$hooks[$name][$id] = $listener;
        }
    }

    static public function removeListener($name, $id)
    {
        if (
            isset(self::$hooks[$name]) &&
            isset(self::$hooks[$name][$id])
        ) {
            unset(self::$hooks[$name]);
        }
    }

    static public function dispatch($name, array $args = array())
    {
        if (isset(self::$hooks[$name]) && !empty(self::$hooks[$name])) {
            foreach (self::$hooks[$name] as $id => $callable) {
                self::invoke($callable, $args, $id);
            }
        }
    }
}