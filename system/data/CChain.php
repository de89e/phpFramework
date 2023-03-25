<?php

namespace framework\system\data;

class CChain
{

    protected $lenth;

    /**
     *
     * [
     * "event"="",
     * "next"=""
     * ]
     *  protected $last_flag = 0;
     * protected $_last_flag;
     *
     */
    protected $data = [];
    protected $pos = 0;
    protected $first = 0;

    public function length()
    {

        $this->lenth = count($this->data);
        return $this->lenth;
    }

    public function push($data = NULL)
    {
        array_push($this->data, $data);
        $this->lenth = count($this->data);
    }

    public function next()
    {
        return next($this->data);
    }

    public function prev()
    {
        return prev($this->data);
    }

    public function current()
    {
        return current($this->data);
    }

    public function first()
    {
        rsort($this->data);
        $value = end($this->data);
        rsort($this->data);
        return $value;
    }

    public function end()
    {
        $value = end($this->data);
        return $value;
    }

    public function pop()
    {
        return array_pop($this->data);
    }

    public function shift()
    {
        return array_shift($this->data);
    }

    public function unshift($data)
    {
        array_unshift($this->data, $data);
    }

    public function reset()
    {
        reset($this->data);
    }

}
