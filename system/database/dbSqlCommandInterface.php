<?php

namespace framework\system\database;

interface dbSqlCommandInterface {

    public function getLastInsertId();

    public function getResultCount();

    public function getResultAffectedCount();

    public function getResultByArray();

    public function getResultByArrayNum();

    public function getResultByArrayAssoc();

    public function getResultByOneObject();

    public function getResultByOneArray();
}
