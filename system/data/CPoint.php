<?php

namespace framework\system\data;

class CPoint {

    protected $points = [];
    protected $values = [];

    public function __construct() {

        $args = func_get_args();
        foreach ($args as $value) {
            array_push($this->points, $value);
        }
    }

    public function getCount() {
        return count($this->points);
    }

    public function getAllPoints() {

        return $this->points;
    }

    public function getValueSum() {
        $sum = 0;
        foreach ($this->values as $value) {
            $sum+=$value;
        }
        return $sum;
    }

    public function getValueProduct() {
        $product = 1;

        foreach ($this->values as $value) {

            $product*=$value;
        }
        return $product;
    }

    public function __set($name = 'x', $value = '') {

        if (is_string($name) && in_array($name, $this->points)) {

            $this->values[$name] = $value;
        }
    }

    public function __get($name) {
        if (array_key_exists($name, $this->values)) {
            return $this->values[$name];
        } else {
            return null;
        }
    }

}
