<?php

namespace classes;

/**
 * Every str are split by ':?' data are in Arrs;
 */

class Db
{
    protected static $config = [];
    private $tableName = '';
    private $whereStr = '';
    private $whereArr = [];
    private $whereCount = 0;
    private $limit = '';
    private $offset = '';
    private $columns = '';
    private $sql = '';
    private $insert = '';
    private $update = '';
    private $data = [];
    private $ext = [];
    private $action = '';
    private $order = '';
    private $group = '';

    private function __construct($name)
    {
        $this->tableName = $name;
    }

    public static function initConfig()
    {
        if (self::$config !== []) return;
        self::$config = require(LZ_ROOT . DS . 'config' . DS . 'database.php');
    }

    public static function table($name)
    {
        self::initConfig();
        return new Db($name);
    }

    public static function name($name)
    {
        self::initConfig();
        return self::table(self::$config['prefix'] . $name);
    }

    public function whereOptions($condition, $option = 'AND')
    {
        $i = 0;
        foreach ($condition as $v) {
            if (gettype($v) !== 'array') throw new \Exception("Condition Must Be an Array in Array", 1);
            $is_between = strtoupper(trim($v[1])) === 'BETWEEN';
            if ($i > 0)
                $this->whereStr .= " $option ";
            if (strtoupper(trim($v[1])) === 'IN') {
                if (gettype($v[2]) == 'array') $v[2] = implode(',', $v[2]);
                $this->whereStr .= '`' . $v[0] . '` IN (' . $v[2] . ')';
            } else {
                $this->whereStr .= '`' . $v[0] . '` ' . strtoupper(trim($v[1])) . ' :W' . $this->whereCount . ($is_between ? ' AND :W' . (++$this->whereCount) : '');
                $this->whereArr = array_merge($this->whereArr, ($is_between ? ['W' . ($this->whereCount - 1) => $v[2], 'W' . $this->whereCount => $v[3]] : ['W' . $this->whereCount => $v[2]]));
            }
            $this->whereCount++;
            $i++;
        }
        return $this;
    }

    public function where($cond)
    {
        return $this->whereOptions($cond, 'AND');
    }
    public function whereOr($cond)
    {
        return $this->whereOptions($cond, 'OR');
    }

    public function order($column, $order = 'DESC')
    {
        $this->order = " ORDER BY $column $order ";
        return $this;
    }

    public function group($column)
    {
        $this->group = " GROUP BY $column";
        return $this;
    }

    public function select($columns = '*')
    {
        $this->columns = $columns;
        $this->build_sql('select');
        $this->action = 'select';
        return $this->exec();
    }

    public function insert($data)
    {
        $insert = ' (';
        $tmpStr = '(';
        foreach ($data as $k => $v) {
            $insert .= '`' . $k . '`, ';
            $tmpStr .= ':' . $k . ', ';
        }
        $insert = preg_replace("/,\ $/i", ')', $insert);
        $tmpStr = preg_replace("/,\ $/i", ')', $tmpStr);
        $this->insert = $insert . ' VALUES ' . $tmpStr;
        $this->ext = array_merge($this->ext, $data);
        $this->build_sql('insert');
        $this->action = 'insert';
        return $this->exec();
    }

    public function update($data)
    {
        $tmpStr = ' SET ';
        foreach ($data as $k => $v) {
            $tmpStr .= '`' . $k . '`=:' . $k . ', ';
        }
        $tmpStr = preg_replace("/,\ $/i", ' ', $tmpStr);
        $this->update = $tmpStr;
        $this->ext = array_merge($this->ext, $data);
        $this->build_sql('update');
        $this->action = 'update';
        return $this->exec();
    }

    public function delete()
    {
        $this->build_sql('delete');
        $this->action = 'delete';
        return $this->exec();
    }

    public function build_sql($option = '')
    {
        $sql_directions = [
            'select' => 'SELECT ' . $this->columns . ' FROM ',
            'insert' => 'INSERT INTO ',
            'update' => 'UPDATE ',
            'delete' => 'DELETE FROM ',
        ];
        $sql = $sql_directions[$option] . '`' . $this->tableName . '`';
        if ($option === 'insert') {
            $sql .=  $this->insert;
        }
        if ($option === 'update') {
            $sql .=  $this->update;
        }
        if (trim($this->whereStr) !== '') {
            $sql .= ' WHERE ' . $this->whereStr;
            $this->ext = array_merge($this->ext, $this->whereArr);
        }
        if (trim($this->order) !== '') {
            $sql .= $this->order;
        }
        if (trim($this->group) !== '') {
            $sql .= $this->group;
        }
        $this->sql = $sql;
        return $sql;
    }

    public function exec()
    {
        // var_dump($this->sql, $this->ext);
        return $this->data($this->sql, $this->ext);
    }

    private function data($sql, $ext)
    {
        $dbh = new \PDO('mysql:host=' . Db::$config['host'] . ';dbname=' . Db::$config['dbname'], Db::$config['user'], Db::$config['password']);
        $dbh->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false); //禁用prepared statements的仿真效果
        $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $dbh->exec("set names 'utf8'");
        $stmt = $dbh->prepare($sql);
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