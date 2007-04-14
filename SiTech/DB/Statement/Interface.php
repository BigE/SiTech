<?php
interface SiTech_DB_Statement_Interface
{
    public function bindColumn($column, &$var, $type=null);
    public function bindParam($param, &$var, $type=null, $length=null, $options=array());
    public function bindValue($param, $val, $type=null);
    public function closeCursor();
    public function columnCount();
    public function errorCode();
    public function errorInfo();
    public function execute($params=array());
    public function fetch($fetchStyle=null, $cursor=null, $offset=null);
    public function fetchAll($fetchStyle=null, $column=0);
    public function fetchColumn($column=0);
    public function fetchObject($class=null, $args=array());
    public function getAttribute($attr);
    public function getColumnMeta($column);
    public function nextRowset();
    public function rowCount();
    public function setAttribute($attr, $value);
    public function setFetchMode($mode, $arg1=null, $arg2=null);
}
?>
