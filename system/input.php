<?php namespace System;

class Input {

    /**
     * The input data for the request.
     *
     * @var array
     */
    public static $input;

    /**
     * Determine if the input data contains an item or set of items.
     *
     * @return bool
     */
    public static function has()
    {
        foreach (func_get_args() as $key) {
            if (is_null(static::get($key))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the input data contains an item or set of items that are not empty.
     *
     * @return bool
     */
    public static function filled()
    {
        foreach (func_get_args() as $key) {
            if (!static::has($key) || trim((string) static::get($key)) == '') {
                return false;
            }
        }

        return true;
    }

    /**
     * Get an item from the input data.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return string
     */
    public static function get($key = null, $default = null)
    {
        if (is_null(static::$input)) {
            static::hydrate();
        }

        return static::from_array(static::$input, $key, $default);
    }

    /**
     * Determine if the old input data contains an item or set of items.
     *
     * @return bool
     */
    public static function had()
    {
        foreach (func_get_args() as $key) {
            if (is_null(static::old($key))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the old input data contains an item or set of items that are not empty.
     *
     * @return bool
     */
    public static function was_filled()
    {
        foreach (func_get_args() as $key) {
            if (!static::had($key) || trim((string) static::old($key)) == '') {
                return false;
            }
        }

        return true;
    }

    /**
     * Get input data from the previous request.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return string
     */
    public static function old($key = null, $default = null)
    {
        if (Config::get('session.driver') == '') {
            throw new \Exception("Sessions must be enabled to retrieve old input data.");
        }

        return static::from_array(Session::get('laravel_old_input', array()), $key, $default);
    }

    /**
     * Get an item from an array. If no key is specified, the entire array will be returned.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $default
     * @return string
     */
    private static function from_array($array, $key, $default)
    {
        if (is_null($key)) {
            return $array;
        }

        return (array_key_exists($key, $array)) ? $array[$key] : $default;
    }

    /**
     * Hydrate the input data for the request.
     *
     * @return void
     */
    public static function hydrate()
    {
        switch (Request::method()) {
            case 'GET':
                static::$input =& $_GET;
                break;

            case 'POST':
                static::$input =& $_POST;
                break;

            case 'PUT':
            case 'DELETE':
                if (isset($_POST['request_method']) && ($_POST['request_method'] == 'PUT' || $_POST['request_method'] == 'DELETE')) {
                    static::$input =& $_POST;
                } else {
                    parse_str(file_get_contents('php://input'), static::$input);
                }
        }
    }
}
