## 自己突发奇想写的一套 MVC 开发框架

目前还在开发阶段，model 还不是很完善

目前这套框架兼容 php5.4+ ，包括 PHP7

## 安装运行

在命令行窗口或控制台 输入

```bash
git clone https://github.com/undercake/t-framework
```

**在 mac 或者 linux 环境下面，注意需要设置 cache 目录权限为 777**
**在 windows 环境，需要设置 cache 目录拥有读写权限，否则会报错**

## 访问路径

项目访问路径：www.xxx.com/index.php/controller/action
**目前暂不支持命令行访问**

index.php 入口文件

controller 控制器

action 方法

## 视图

视图使用 smarty 引擎
使用时控制器需继承 Base 类，注册值使用

```php
$this->assign($key,$value)
```

输出视图时使用

```php
$this->view()
```

## 控制器

控制器目录位于 /controller 文件夹内
使用时继承 \controller\Base 基类

## 常量说明

LZ_ROOT： 框架的根目录
DS： 等同于 DIRECTORY_SEPARATOR 系统目录分隔符
CONFIG_ROOT： 配置文件存放目录

## 流程说明

0. 系统访问网址
1. Router 类解析网址，得到控制器名称和方法名称
2. 检查控制器和方法的可访问性，若存在，则调用，不存在则显示 404 页面
3. 返回调用后的内容

## Db 库

要使用 Db 类必须使用 \classes\Db 调用
数据库操作统一入口： Db::

**目前只支持简单查询方法，复杂查询后续会更新**

配置文件：config/database.php

查询方法：
示例 1：

```sql
SELECT * FROM `table_name`
```

等同于

```php
Db::table('table_name')->select();
```

示例 2：

```sql
SELECT `col1`,`col2` FROM `table_name`
```

等同于

```php
Db::table('table_name')->select('col1,col2');
```

示例 3：

```sql
SELECT * FROM `table_name` WHERE `col3`='val'
```

等同于

```php
Db::table('table_name')->where([['col3','=','val']])->select();
```

插入方法：

```sql
INSERT INTO `shop_goods`(`col1`,`col2`) VALUES ('val1','val2')
```

等同于

```php
$data = ['col1'=>'val1','col2'=>'val2'];
Db::table('shop_goods')->insert($data);
```

update:

```sql
UPDATE `shop_goods` SET `col1`='val1', `col2`='val1' WHERE `id`=2;
```

等同于

```php
$data = ['col1'=>'val1','col2'=>'val2'];
Db::table('shop_goods')->where([['id','=',2]])->insert($data);
```

delete:

```sql
DELETE FROM `shop_goods` WHERE `id`=2 AND `user`=1;
```

等同于

```php
Db::table('shop_goods')->where([['id', '=', 2],['user', '=', 1]])->delete();
```

## Session 库

session 库的配置文件：config/session.php
要使用 Session 类必须使用 \classes\Session 调用
Session 使用前需调用 Session::init()来初始化 session

设置变量：
设置变量需要一个 key 对应一个 value
value 值接收的类型为 string | boolean | int | float

```php
Session::set($key, $value);
```

获取变量：
如果 key 值的 session 变量不存在则返回 $default_value
如果 key 值留空，则返回所有 session 变量

```php
Session::get([$key [, $default_value]]);
```
