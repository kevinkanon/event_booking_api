<?php

namespace App\Service;

class PDO extends \PDO {

    const PARAM_ARRAY_INT = 11;
    const PARAM_ARRAY_STR = 12;
    const PARAM_ARRAY_LOB = 13;
    const PARAM_ARRAY_BOOL = 15;

    protected $driver_options;

    /**
     *
     * @param string $statement
     * @param array $driver_options
     */
    public function prepare($statement, $driver_options = array()) {
        $this->driver_options = $driver_options;

        return new PDOStatement($statement, $this);
    }

    public function nativePrepare ($statement)
    {
        return parent::prepare($statement, $this->driver_options);
    }

    public function isArrayType($type)
    {
        return
            $type === self::PARAM_ARRAY_INT ||
            $type === self::PARAM_ARRAY_STR ||
            $type === self::PARAM_ARRAY_LOB ||
            $type === self::PARAM_ARRAY_BOOL;
    }
}