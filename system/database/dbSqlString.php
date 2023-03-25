<?php

namespace framework\system\database;

class dbSqlString extends dbObject
{

    public $sqlString = '';
    public $sqlStrings = array();
    private $dist = false;
    private $option = 'select';
    private $condition_array = array();
    private $expression = array();
    private $tables = array();
    private $cv = array();
    public $sql_prifix = '';
    public $sql_prifix_symbol = '';
    private $sqlStringInit = 0;
    private $values_colum = [];

    public function toString()
    {

        /*
         * 生成语句
         */
        if ($this->sqlStringInit) {
            $this->generate();

            /*
            if (!empty($this->sql_prifix)) {
            $this->sqlString = str_replace($this->sql_prifix_symbol, $this->sql_prifix, $this->sqlString);
            } else {
            $this->sqlString = str_replace($this->sql_prifix_symbol, '', $this->sqlString);
            }
             */
            $this->sqlStringInit = 0;
            return $this->sqlString;
        }
        return 'SqlString Not Initialized';
    }

    public function init()
    {
        /*
         * 替换前缀
         */
        $this->sql_prifix_symbol = SQL_STR_PRIFIX_SYMBOL;

        if (defined('SQL_STR_PRIFIX')) {
            $this->sql_prifix = SQL_STR_PRIFIX;
        }

        $this->clean();
        $this->sqlStringInit = 1;
    }

    private function clean()
    {
        $this->expression = array();
        $this->condition_array = array();
        $this->tables = array();
        $this->cv = array();
        $this->values_colum = [];
    }

    public function establishStart($flag)
    {

        $this->sqlString[$flag] = null;
    }

    public function establishEnd()
    {

    }

    private function option()
    {
        return $this->option;
    }

///////////////////////////////////////////////////////////////////////////////
    //SET
    //SELECT INSERT UPDATE DELETE
    //function start
    ///////////////////////////////////////////////////////////////////////////////
    public function setSelect($column, $dist = false)
    {

        $this->option = 'SELECT';
        $this->dist = $dist;

        $this->expression[] = ' ' . $column . ' ';
    }

    public function setInsert($column, $value = null, $format = '%s')
    {
        $cv = [$column, $format, $value];
        $this->cv[] = $cv;
        $this->option = 'INSERT';
    }

    public function setUpdate($column, $value = null, $format = '%s')
    {
        $cv = [$column, $format, $value];
        $this->cv[] = $cv;
        $this->option = 'UPDATE';
    }

    public function setDelete()
    {

        $this->option = 'DELETE';
    }

///////////////////////////////////////////////////////////////////////////////
    //SET
    //SELECT INSERT UPDATE DELETE
    //function end
    ///////////////////////////////////////////////////////////////////////////////
    public function setTable($tableArray = null)
    {
        if (is_string($tableArray)) {

            $this->tables[] = trim($tableArray);

        } elseif (is_array($tableArray)) {
            foreach ($tableArray as $key => $value) {
                $this->tables[] = trim($value);
            }
        }
    }

    private function expression()
    {

        $expression = $this->expression;
        $SQL = $expression[0];
        for ($i = 1;!empty($expression[$i]); $i++) {
            $SQL .= ',' . $expression[$i];
        }
        return $SQL;
    }

    public function setWhere($conditionFormat, $value)
    {
        $this->setCondition('where', $conditionFormat, $value);
    }

    public function setC($how, $conditionFormat, $value = null)
    {
        $this->setCondition($how, $conditionFormat, $value);
    }

    public function setCondition($how, $conditionFormat, $value = null)
    {
        $this->condition_array[] = array($how, $conditionFormat, $value);
    }

    ///////////////////////////////////////////////////////////////////////////
    //
    //开始生成SQL语句
    //
    ///////////////////////////////////////////////////////////////////////////
    private function generate()
    {
        $SQL = '';
        if (strtoupper($this->option) == 'UPDATE') {
            $SQL = $this->option() . $this->Tables();

            $SQL = $SQL . ' SET ';

            $SQL = $SQL . $this->CV();

            $SQL = $SQL . $this->Condition();
            $this->sqlString = $SQL;
            return;
        }
        if (strtoupper($this->option) == 'SELECT') {
            $SQL = $this->option();
            if ($this->dist) {
                $SQL = $SQL . ' DISTINCT';
            }
            $SQL = $SQL . $this->expression();
            $SQL = $SQL . ' FROM ';
            $SQL = $SQL . $this->Tables();
            $SQL = $SQL . $this->Condition();
            $this->sqlString = $SQL;
            return;
        }
        if (strtoupper($this->option) == 'INSERT') {

            $SQL = $this->option();
            $SQL = $SQL . ' INTO ';
            $SQL = $SQL . $this->Tables();

            $SQL = $SQL . $this->CV();

            $SQL = $SQL . $this->Condition();
            $this->sqlString = $SQL;
            return;
        }
        if (strtoupper($this->option) == 'DELETE') {

            $SQL = $this->option();
            $SQL = $SQL . ' FROM ';
            $SQL = $SQL . $this->Tables();

            $SQL = $SQL . $this->Condition();
            $this->sqlString = $SQL;
            return;
        }
    }

    public function setValues($colum, $value)
    {
        if (isset($this->values_colum[$colum])) {
            $this->values_colum[$colum][] = $value;
        } else {
            $this->values_colum[$colum] = [$value];
        }
    }

    ///////////////////////////////////////////////////////////////////////////
    //
    //结束生成SQL语句
    //
    ///////////////////////////////////////////////////////////////////////////
    protected function CV()
    {
        $values_array = $this->cv;
        $type = $this->option;
        if (strtolower($type) == 'insert') {

            $SQL = '(';
            for ($i = 0;!empty($values_array[$i]); $i++) {

                if ($i == 0) {
                    $SQL .= '';
                    $SQL .= $values_array[$i][0];
                } else {
                    $SQL .= ',';
                    $SQL .= $values_array[$i][0];
                }
            }
            $SQL .= ')VALUES(';
            for ($i = 0;!empty($values_array[$i]); $i++) {

                $value = $values_array[$i][2];
                $value_orgin = $values_array[$i][1];
                $stand = false;
                if (is_array($value)) {

                    if (isset($value['format'])) {
                        $values_array[$i][1] = $value['format'];
                    } else {
                        $values_array[$i][1] = '%s';
                    }
                    if (isset($value['stand'])) {
                        $stand = $value['stand'];
                    }

                    $values_array[$i][2] = $value_orgin;

                }
                if ($i == 0) {
                    $SQL .= '';
                    if ($stand) {
                        $SQL .= '' . sprintf($values_array[$i][1], $values_array[$i][2]) . '';
                    } else {
                        $SQL .= "'" . sprintf($values_array[$i][1], $values_array[$i][2]) . "'";
                    }
                } else {
                    $SQL .= ',';
                    if ($stand) {
                        $SQL .= '' . sprintf($values_array[$i][1], $values_array[$i][2]) . '';
                    } else {
                        $SQL .= "'" . sprintf($values_array[$i][1], $values_array[$i][2]) . "'";
                    }
                }
            }
            $SQL .= ')';
            if (!empty($this->values_colum)) {
                $new_colum_array = [];
                foreach ($this->values_colum as $colum => $values) {
                    $i = 0;
                    foreach ($this->values_colum[$colum] as $colum_value) {
                        $new_colum_array[$i][$colum] = $colum_value;
                        $i++;
                    }
                }
                foreach ($new_colum_array as $colum_array) {
                    $SQL .= ',(';
                    $count = 0;
                    foreach ($colum_array as $value) {
                        if ($count > 0) {
                            $SQL .= ',';
                        }
                        $SQL .= '"' . $value . '"';
                        $count++;
                    }
                    $SQL .= ')';
                }
            }
        }
        if (strtolower($type) == 'update') {
            $SQL = '';
            for ($i = 0;!empty($values_array[$i]); $i++) {
                $value = $values_array[$i][2];
                $value_orgin = $values_array[$i][1];
                $stand = false;
                if (is_array($value)) {

                    if (isset($value['format'])) {
                        $values_array[$i][1] = $value['format'];
                    } else {
                        $values_array[$i][1] = '%s';
                    }
                    if (isset($value['stand'])) {
                        $stand = $value['stand'];
                    }
                    $values_array[$i][2] = $value_orgin;
                    if ($stand) {
                        if ($i == 0) {
                            $SQL .= ' ';
                            $SQL .= $values_array[$i][0] . ' = ' . sprintf($values_array[$i][1], $values_array[$i][2]);
                        } else {
                            $SQL .= ' ,';
                            $SQL .= $values_array[$i][0] . ' = ' . sprintf($values_array[$i][1], $values_array[$i][2]);
                        }
                    }
                } else {
                    if ($i == 0) {
                        $SQL .= ' ';
                        $SQL .= $values_array[$i][0] . " = '" . sprintf($values_array[$i][1], $values_array[$i][2]) . "'";
                    } else {
                        $SQL .= ' ,';
                        $SQL .= $values_array[$i][0] . " = '" . sprintf($values_array[$i][1], $values_array[$i][2]) . "'";
                    }
                }
            }
        }
        return $SQL;
    }

    protected function Condition()
    {
        $condition_array = $this->condition_array;
        $SQL = '';
        /**
         *
         *
         */
        for ($i = 0;!empty($condition_array[$i]); $i++) {
            if (strtolower($condition_array[$i][0]) == 'join') {
                $SQL .= ' ' . sprintf($condition_array[$i][1], $condition_array[$i][2]);
            }
        }
        /**
         *
         *
         */
        for ($i = 0;!empty($condition_array[$i]); $i++) {
            if (strtolower($condition_array[$i][0]) == 'where') {
                $SQL .= ' WHERE ' . sprintf($condition_array[$i][1], $condition_array[$i][2]);
            }
        }

        /**
         *
         *
         */
        for ($i = 0;!empty($condition_array[$i]); $i++) {
            if (strtolower($condition_array[$i][0]) == 'and') {
                $SQL .= ' AND ' . sprintf($condition_array[$i][1], $condition_array[$i][2]);
            }
        }

        /**
         *
         *
         */
        for ($i = 0;!empty($condition_array[$i]); $i++) {
            if (strtolower($condition_array[$i][0]) == 'or') {
                $SQL .= ' OR ' . sprintf($condition_array[$i][1], $condition_array[$i][2]);
            }
        }

        /**
         *
         *
         */
        for ($i = 0;!empty($condition_array[$i]); $i++) {
            if (strtolower($condition_array[$i][0]) == 'not') {
                $SQL .= ' NOT ' . sprintf($condition_array[$i][1], $condition_array[$i][2]);
            }
        }

        /**
         * $sqlString->setC('order by','%s','ID DESC');
         *
         */
        for ($i = 0;!empty($condition_array[$i]); $i++) {
            if (strtolower($condition_array[$i][0]) == 'group by') {
                $SQL .= ' GROUP BY ' . sprintf($condition_array[$i][1], $condition_array[$i][2]);
            }
        }
        /**
         * $sqlString->setC('order by','%s','ID DESC');
         *
         */
        for ($i = 0;!empty($condition_array[$i]); $i++) {
            if (strtolower($condition_array[$i][0]) == 'order by') {
                $SQL .= ' ORDER BY ' . sprintf($condition_array[$i][1], $condition_array[$i][2]);
            }
        }

        /**
         *
         *
         */
        for ($i = 0;!empty($condition_array[$i]); $i++) {
            if (strtolower($condition_array[$i][0]) == 'limit') {
                $SQL .= ' Limit ' . sprintf($condition_array[$i][1], $condition_array[$i][2]);
            }
        }
//$SQL = 'WHRER ID = 1';
        return ' ' . $SQL;
    }

    public function Tables()
    {
        $tables = $this->tables;
        $SQL = $tables[0];
        for ($i = 1;!empty($tables[$i]); $i++) {
            $SQL .= ',' . $tables[$i];
        }

        if (!empty($this->sql_prifix)) {
            $SQL = str_replace($this->sql_prifix_symbol, $this->sql_prifix, $SQL);
        } else {
            $SQL = str_replace($this->sql_prifix_symbol, '', $SQL);
        }

        return ' ' . $SQL . ' ';
    }

}
