<?php

namespace classes;

use classes\Request as Req;

class Session
{
    private static $ss = null;
    private $data = null;
    private $config = [];
    private $id = '';
    private $is_destroyed = false;

    private function __construct()
    {
        $this->config = require(LZ_ROOT . DS . 'config' . DS . 'session.php');
        $id = Req::headers('Cookie', false);
        $this->id = $id ? str_replace($this->config['prefix'] . '=', '', $id) : false;
        $time = time();
        if (!$this->id) {
            $this->id = sha1(Req::getIp() . time() . microtime() . Req::path() . randomString(128));
            header("Set-Cookie: {$this->config['prefix']}={$this->id}; path={$this->config['cookie_path']}");
            $rs = file_put_contents($this->config['file_path'] . DS . $this->id, json_encode(['start_time' => $time]));
            if (false === $rs) {
                give_500('Session 写入失败');
            }
        }
        $expires = $time + $this->config['expires'];
        $data = file_get_contents($this->config['file_path'] . DS . $this->id);
        $this->data = $data ? json_decode($data, true) : ['start_time' => $time];
        $this->data['expires'] = $expires;
    }

    static function init()
    {
        if (!self::$ss) self::$ss = new Session;
        return self::$ss;
    }

    static function get($key = '', $default = null)
    {
        $data = [];
        foreach (self::$ss->data as $k => $v) {
            if ($k == 'start_time' || $k == 'expires') continue;
            $data[$k] = $v;
        }
        return trim($key) == '' ? $data : (isset($data[$key]) ? $data[$key] : $default);
    }

    static function set($key, $value)
    {
        if ($key == 'start_time' || $key == 'expires') return;
        self::$ss->data[$key] = $value;
    }

    static function delete($key)
    {
        if ($key == 'start_time' || $key == 'expires') return;
        unset(self::$ss->data[$key]);
    }

    static function clean()
    {
        self::$ss->data = [];
    }

    static function gc()
    {
        $config = require(LZ_ROOT . DS . 'config' . DS . 'session.php');
        $path = $config['file_path'];
        $count = 0;
        $handler = opendir($path);
        $time = time();
        while (($filename = readdir($handler)) !== false) { //务必使用!==，防止目录下出现类似文件名“0”等情况
            if ($filename != "." && $filename != "..") {
                $text = file_get_contents($path . DS . $filename);
                $data = $text ? (json_decode(trim($text), true)) : '';
                $is_deleteable = $text ? (isset($data['expires']) ? ($data['expires'] < $time) : true) : true;
                $count += $is_deleteable ? unlink($path . DS . $filename) : 0;
            }
        }
        closedir($handler);
        return $count;
    }

    static function destroy()
    {
        self::$ss->is_destroyed = true;
        unlink(self::$ss->config['file_path'] . DS . self::$ss->id);
    }

    function __destruct()
    {
        if (!$this->is_destroyed) {
            $rs = file_put_contents($this->config['file_path'] . DS . $this->id, json_encode($this->data));
            if (false === $rs) {
                give_500('Session 写入失败');
            }
        }
    }
}