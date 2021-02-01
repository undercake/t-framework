## 自己突发奇想写的一套 MVC 开发框架

目前还在开发阶段，model 还不是很完善

**后续会不断完善该项目框架**

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
$this->assign($key,$value);
```

输出视图时使用

```php
$this->view();
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

where 语句：
where 接受的参数是一个二维数组，第二层数组为：

```php
$col_name = "col_1";
$condition = '='; // 可选值为['=', '<', '>', 'BETWEEN']
$value = 'val';
where([$col_name, $condition, $value]);//  WHERE 'col_1'='val'
// 当 condition 为 BETWEEN 时须传入第四个参数
where(['col2', 'BETWEEN', 1, 3]); // WHERE 'col2' BETWEEN 1 AND 3
```

查询方法：select
select 不输入任何值的时候等同于“\*”，如果输入字符串，则表示要选择的列
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

插入方法：insert

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
value 值接收的类型为 string | boolean | int | double

```php
Session::set($key, $value);
```

获取变量：
如果 key 值的 session 变量不存在则返回 $default_value
如果 key 值留空，则返回所有 session 变量

```php
Session::get([$key[, $default_value]]);
```

## 助手函数

#### json()

输出 json 字符串的助手函数

```php
json(array<string|int> $rs) : mixed
```

参数：

```php
$rs : array<string|int>
```

#### dump()

打印变量数组函数

#### is_ajax() : bool

判断当前请求是否为 ajax

#### randomString()

生成随机字符串

```php
randomString([int $length = 10 ]) : string
```

参数：

```php
$length : int = 10
```

要生成的字符串长度

#### give_error()

输出错误信息并终止程序

```php
give_error(number|string $code, array<string|int>|string $msg) : mixed
```

参数：

```php
$code : number|string // HTTP 错误码
$msg : array<string|int>|string // 当前请求判断为 ajax 请求时， $msg 需为数组，为一般请求时，$msg 需为字符串
```

#### give_404() : exit

输出 404 信息

#### give_403() : exit

输出 403 信息

#### give_500()

输出服务器 500 信息

```php
give_500([string|array<string|int>|bool $arr = false ]) : exit
```

参数：

```php
$arr : string|array<string|int>|bool = false
```

#### get_config()

获取配置信息

```php
get_config([string $key = '' ]) : array<string|int>
```

参数：

```php
$key : string = '' // 获取的配置信息
```

#### put_file_contents()

写入文件助手函数

```php
put_file_contents(string $filename, string $data[, bool $rewrite = true ][, bool $auto_touch_file = false ]) : mixed
```

参数：

```php
$filename : string // 文件名称
$data : string                  // 要写入的内容
$rewrite : bool = true          // 是否覆盖写入
$auto_touch_file : bool = false // 文件不存在时自动创建
```

#### init_array()

比较数组，并返回以后者为基准的数组，如果前者有某个成员，则优先用前者，如果没有，则用后者作为代替值

```php
init_array([array<string|int> $array = [] ][, array<string|int> $origin = [] ]) : array<string|int>
```

参数：

```php
$array : array<string|int> = []  // 初始数组，该数组可能有缺失或者冗余
$origin : array<string|int> = [] // 基准数组
```
