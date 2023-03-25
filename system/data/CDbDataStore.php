<?php

namespace framework\system\data;

class CDbDataStore extends CModel {

    public $data;
    public $temp_data;
    public $o_data;
    public $mapping;
    public $mapping_data = array();
    public $dataRoot = 'data';
    public $totalProperty = 0;

    public function dataRoot($flag = 'data') {
        $this->data[$flag] = $this->data['data'];
        $this->dataRoot = $flag;
    }

    public function loadArray($array) {
        $this->o_data[$this->dataRoot] = $this->data[$this->dataRoot] = $array;
        $this->totalProperty = count($array);
        $this->data['totalProperty'] ="$this->totalProperty ";
        $this->mapping();
    }

    public function setProperty($name, $value = null) {
        if (empty($name)) {
            return FALSE;
        }
        $this->data[$name] = $value;
    }

    public function createMapping($mapping = array()) {
        if (!is_array($mapping)) {
            return;
        }
        $this->mapping = $mapping;
    }

    protected function mapping() {

        if (!empty($this->mapping)) {
            $data = array();
            for ($i = 0; !empty($this->mapping[$i]); $i++) {
                $name = $this->mapping[$i]['name'];
                //render
                $render = isset($this->mapping[$i]['render']) ? $this->mapping[$i]['render'] : NULL;
                //render
                for ($ii = 0; !empty($this->data[$this->dataRoot][$ii]); $ii++) {
                    if ($this->mapping[$i]['mapping'] == "null") {
                        $mapped_row_data = "";
                    } else {
                        $mapped_row_data = $this->data[$this->dataRoot][$ii][$this->mapping[$i]['mapping']];
                    }
                    //render
                    if ($render) {
                        $mapped_row_data = $render($mapped_row_data, $this->data[$this->dataRoot][$ii]);
                    }
                    //render
                    $data[$name][] = $mapped_row_data;
                }
            }
            $this->mapping_data = $data;

            $this->data['mapped'] = TRUE;
        }
    }

    public function getRow($num = 0) {
        $row = array();
        if ($this->data['mapped']) {
            foreach ($this->mapping_data as $key => $value) {
                $row[$key] = isset($value[$num]) ? $value[$num] : NULL;
            }
            $this->temp_data = $this->data;
            $this->temp_data[$this->dataRoot] = array();
            $this->temp_data[$this->dataRoot][] = $row;
            return $row;
        }
        return $this->data[$this->dataRoot][$num];
    }

    public function getRows($start = 0, $limit = 1) {
        $rows = array();
        if ($this->data['mapped']) {

            for ($i = $start; $i < ($start + $limit); $i++) {
                $rows[] = $this->getRow($i);
            }
            $this->temp_data = $this->data;
            $this->temp_data[$this->dataRoot] = array();
            $this->temp_data[$this->dataRoot] = $rows;
            return $rows;
        }

        return $this->data[$this->dataRoot];
    }

    public function getAll() {
        $data = array();
        if ($this->data['mapped']) {
            $count = count($this->data[$this->dataRoot]);
            for ($i = 0; $i <= $count - 1; $i++) {
                $data[] = $this->getRow($i);
            }
            return $data;
        }
        return $this->data[$this->dataRoot];
    }

    public function getArray() {
        $data = '';
        if (empty($this->temp_data)) {
            $this->data[$this->dataRoot] = $this->getAll();
            $data = $this->data;
        } else {
            $data = $this->temp_data;
        }
        return $data;
    }

    public function getCount() {
        return $this->totalProperty;
    }

    public function getTotalProperty() {
        return $this->totalProperty;
    }

    public function getJSON() {
        $data = '';
        if (empty($this->temp_data)) {
            $this->data[$this->dataRoot] = $this->getAll();
            $data = $this->data;
        } else {
            $data = $this->temp_data;
        }

        return json_encode($data);
    }

}
