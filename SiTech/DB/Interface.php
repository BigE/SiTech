<?php
interface SiTech_DB_Interface
{
    public function beginTransaction();
    public function commit();
    public function errorCode();
    public function errorInfo();
    public function exec($sql);
    public function getAttribute($attr);
    public function lastInsertId($name=null);
    public function prepare($sql, $args=array());
    public function query($sql, $mode=null, $arg1=null, $arg2=null);
    public function quote($string, $mode=null);
    public function rollBack();
    public function setAttribute($attr, $value);
}
?>
