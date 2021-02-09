<?php

namespace drivers;

use Exception;

class MySql
{
    private $host = 'localhost';
    private $db_name = '';
    private $user_name = '';
    private $password = '';

    public function __construct($config = [])
    {
        if (!class_exists('PDO')) throw new Exception('PDO class not exists!');
        $this->host = isset($config['host']) ? $config['host'] : 'localhost';
        $this->db_name = isset($config['db_name']) ? $config['db_name'] : '';
        $this->user_name = isset($config['user_name']) ? $config['user_name'] : '';
        $this->password = isset($config['password']) ? $config['password'] : '';
    }

    public function exec($sql, $ext = [])
    {
        $dbh = new \PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->user_name, $this->password);
        $dbh->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false); //禁用prepared statements的仿真效果
        $dbh->setAttribute(\PDO::ATTR_ERRMODE, get_config('common')['debug'] ? \PDO::ERRMODE_SILENT : \PDO::ERRMODE_EXCEPTION);
        $dbh->exec("set names 'utf8'");
        $stmt = $dbh->prepare($sql);
        if (!$stmt) return false;
        $exeres = $stmt->execute($ext);
        $data = array();
        if ($exeres) {
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            if ($this->action == 'insert') {
                $data = $dbh->lastInsertId();
            }
            if (count($data) == 0) {
                $data = $stmt->rowCount();
            }
        } else {
            $data[0] = $dbh->errorInfo();
            $data[1] = $stmt->errorInfo();
        }
        $dbh = null;
        return $data;
    }
}