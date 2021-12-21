<?php
require '../vendor/autoload.php';
use Ass\request\http\General;
class Test
{
    public function a()
    {
        $client = new General('http://kang.live');
        $re1 = $client->getQuery('/pat/test/ac',['name' => '张三','age' => 18,'gender' => '男'])->send();
        $re2 = $client->getQuery('/pat/test/ac',['name' => '张三','age' => 18,'gender' => '男'])->send();
        print_r($re1);
        print_r($re2);

        $client2 = new General('http://test.api.datacenter.miaoshou.com/v1/');
        $re3 = $client2->getQuery('test/cc1')->send();
        print_r($re3);
    }

    //携带cookies
//    public function b()
//    {
//        $client = new General('https://broker-serve.miaoshou.com',['cookies' => [ 'MiaoBaseAdminUser' => '%7B%22id%22%3A948%2C%22username%22%3A%22kangliyang%22%2C%22realname%22%3A%22%5Cu5eb7%5Cu7acb%5Cu6768%22%2C%22type%22%3A1%2C%22ttl_time%22%3A1639764104%2C%22encryption_key%22%3A%2275eed17cb25addeb665dee9dec4e2987%22%7D']]);
//        $re = $client->sendGet('/jjr/info/detail',['id' => 101]);
//        print_r($re);
//    }
//
//    //application/x-www-form-urlencoded POST
//    public function c()
//    {
//        $client = new General('http://kang.live');
//        $re = $client->sendPostFormUrlencoded('/pat/test/ad',['name' => '张三','age' => 18,'gender' => '男']);
//        print_r($re);
//    }
//
//    //multipart/form-data POST
//    public function d()
//    {
//        $client = new General('http://kang.live');
//        $re = $client->sendPostFormData('/pat/test/b1',['name' => '张三','age' => 18,'gender' => '男'],['file' => 'E:\image\789.jpg']);
//        print_r($re);
//    }
//
//    //http base auth 认证
//    public function e()
//    {
//        $client = new General('http://kang.live');
//        $re = $client->sendGetAuth('/pat/test/b2',['sb','123456789']);
//        print_r($re);
//    }
//
//    //JSON 数据
//    public function f()
//    {
//        $client = new General('http://kang.live');
//        $re = $client->sendPostJson('/pat/test/b3',['name' => '张三','age' => 18]);
//        print_r($re);
//    }
//
    //并发测试
    public function dd()
    {
        $clientA = new \Ass\request\http\Concurrent('http://kang.live');
        $a = $clientA->getQuery('aa','/pat/test/ac',['name' => '张三','age' => 18,'gender' => '男']);
        $b = $clientA->getQuery('bb','/pat/test/ac',['name' => '张三','age' => 18,'gender' => '男']);
        $clientB = new \Ass\request\http\Concurrent('http://test.api.datacenter.miaoshou.com/v1/');
        $c = $clientB->getQuery('cc','test/cc1');
        $request = new \Ass\request\http\ConcurrentRequest();
        $re = $request->send($a,$b,$c);
        print_r($re);


    }

}

$aa = new Test();
$aa->dd();

