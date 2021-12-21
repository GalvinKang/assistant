
PHP HTTP Client 简单封装

依然使用的是libcurl 的扩展，与curl同源


请求示例如下
-------------------

引入 vendor 目录下的 autoload.php 文件
具体参数含义参见 request/http/Concurrent.php 和 General.php

```
1.简单GET请求
$clientA = new General('http://kang.live');
$ac1 = $clientA->getQuery('/pat/test/ac',['name' => '张三','age' => 18,'gender' => '男'])->send();
$ac2 = $clientA->getQuery('/pat/test/ac',['name' => '张三','age' => 18,'gender' => '男'])->send();
print_r($ac1);
print_r($ac2);
$clientB = new General('http://test.api.datacenter.miaoshou.com/v1/');
$cc1 = $client2->getQuery('test/cc1')->send();
print_r($cc1);

返回数据结构如:
Array
(
    [code] => 200
    [msg] => ok
    [data] => Array
        (
            [name] => 张三
            [age] => 18
            [gender] => 男
        )
)
Array
(
    [code] => 200
    [msg] => ok
    [data] => Array
        (
            [name] => 张三
            [age] => 18
            [gender] => 男
        )
)
Array
(
    [code] => 200
    [msg] => 获取成功
    [data] => Array
        (
            [0] => Array
                (
                    [doctor_id] => 3883985
                    [num] => 15
                )
            [1] => Array
                (
                    [doctor_id] => 4219248
                    [num] => 408
                )
        )
)


2.简单并发请求
$clientA = new Concurrent('http://kang.live');
$aa = $clientA->getQuery('aa','/pat/test/ac',['name' => '张三','age' => 18,'gender' => '男']);
$bb = $clientA->getQuery('bb','/pat/test/ac',['name' => '张三','age' => 18,'gender' => '男']);
$clientB = new Concurrent('http://test.api.datacenter.miaoshou.com/v1/');
$cc = $clientB->getQuery('cc','test/cc1');
$request = new ConcurrentRequest();
$re = $request->send($a,$b,$c);
print_r($re);

返回数据结构如:
Array
(
    [aa] => Array
        (
            [code] => 200
            [msg] => ok
            [data] => Array
                (
                    [name] => 张三
                    [age] => 18
                    [gender] => 男
                )
        )

    [bb] => Array
        (
            [code] => 200
            [msg] => ok
            [data] => Array
                (
                    [name] => 张三
                    [age] => 18
                    [gender] => 男
                )
        )

    [cc] => Array
        (
            [code] => 200
            [msg] => 获取成功
            [data] => Array
                (
                    [0] => Array
                        (
                            [doctor_id] => 3883985
                            [num] => 15
                        )
                    [1] => Array
                        (
                            [doctor_id] => 4219248
                            [num] => 408
                        )
                )
        )
)

```
