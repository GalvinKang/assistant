<?php
namespace Ass\test;
require '../vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Utils;
use Psr\Http\Message\ResponseInterface;
use Exception;
use Throwable;
class HttpTest
{
    //普通请求
    public function a()
    {
        $client = new Client([
            'base_uri' => 'http://kang.live',
            // You can set any number of default request options.
            'timeout'  => 2.0,
        ]);
        $response  =  $client->request ( 'GET' ,  '/pat/test/ac' );
//        echo $response->getBody();
        print_r(json_decode($response->getBody())); //对象格式
    }

    //异步请求,用处不大 类似go的xc
    public function b()
    {
        $client = new Client([
            'base_uri' => 'http://kang.live',
            // You can set any number of default request options.
            'timeout'  => 12.0,
        ]);
        $promise  =  $client->requestAsync ( 'GET' ,  '/pat/test/ad' );
        $promise->then(
            function (ResponseInterface $res) {
                echo $res->getStatusCode() . "\n";
                echo $res->getBody();
            },
            function (RequestException $e) {
                echo $e->getMessage() . "\n";
                echo $e->getRequest()->getMethod();
            }
        );
        $promise->wait();
        echo 66666;
    }

    //并发请求
    public function c()
    {
        $t1 = microtime(true);
        $clientC = new Client([
            //'base_uri' => 'http://kang.live/pat/',
            'base_uri' => 'http://test.api.datacenter.miaoshou.com/v1/',
            'timeout'  => 12.0,
        ]);
//        $clientB = new Client([
//            'base_uri' => 'http://test2.broker.serve.miaoshou.com',
//            'timeout'  => 12.0,
//        ]);
        $promises  =  [
//            'cc1' => $clientC->requestAsync ( 'GET' ,  'test/cc5',['query' => ['doctor_ids' => '3883985,4219248']] ),
//            'cc2' => $clientC->requestAsync ( 'GET' ,  'test/cc5',['query' => ['doctor_ids' => '3883985,4219248']] ),
//            'cc3' => $clientC->requestAsync ( 'GET' ,  'test/cc5',['query' => ['doctor_ids' => '3883985,4219248']] ),
//            'cc4' => $clientC->requestAsync ( 'GET' ,  'test/cc5',['query' => ['doctor_ids' => '3883985,4219248']] ),
            'cc1' => $clientC->requestAsync ( 'GET' ,  'test/cc1' ),
            'cc2' => $clientC->requestAsync ( 'GET' ,  'test/cc2' ),
//            'cc3' => $clientC->requestAsync ( 'POST' ,  'test/cc3' ),
//            'cc4' => $clientC->requestAsync ( 'GET' ,  'test/cc4' ),
            //'bb1' => $clientB->requestAsync ( 'GET' ,  '/v1/test/bb1' ),
            //'bb2' => $clientB->requestAsync ( 'GET' ,  '/v1/test/bb2' ),
            //'bb3' => $clientB->requestAsync ( 'GET' ,  '/v1/test/bb3' ),
        ];
        try{
            $responses = Utils::unwrap($promises);
            echo 'cc1-' .  $responses['cc1']->getStatusCode () . "\r\n";
            echo 'cc2-' .  $responses['cc2']->getStatusCode () . "\r\n";
//            echo 'cc3-' .  $responses['cc3']->getStatusCode () . "\r\n";
//            echo 'cc4-' .  $responses['cc4']->getStatusCode () . "\r\n";
            //echo $responses['bb1']->getStatusCode () . "\r\n";
            //echo $responses['bb2']->getStatusCode () . "\r\n";
            //echo $responses['bb3']->getStatusCode () . "\r\n";
            $t2 = microtime(true);
            echo $t2-$t1 . "\r\n";
            //@file_put_contents('E:\data\logs\mysql-300b-linux-111.log',$t2-$t1 . "\r\n",FILE_APPEND);
        } catch (Exception $e) {
            echo $e->getMessage();
        } catch (Throwable $e) {
            echo $e->getMessage();
        }
    }

    //并发请求
    public function c2()
    {
        $t1 = microtime(true);
        $client = new Client([
            //'base_uri' => 'http://kang.live/pat/',
            'base_uri' => 'http://test.api.datacenter.miaoshou.com/v1/',
            'timeout'  => 12.0,
        ]);

//        $client2 = new Client([
//            'base_uri' => 'http://test2.broker.serve.miaoshou.com',
//            'timeout'  => 30.0,
//        ]);
//        $response_cc1 = $client->request ( 'GET' ,  'test/cc5' ,['query' => ['doctor_ids' => '3883985,4219248']]);
//        $response_cc2 = $client->request ( 'GET' ,  'test/cc5' ,['query' => ['doctor_ids' => '3883985,4219248']]);
//        $response_cc3 = $client->request ( 'GET' ,  'test/cc5' ,['query' => ['doctor_ids' => '3883985,4219248']]);
//        $response_cc4 = $client->request ( 'GET' ,  'test/cc5' ,['query' => ['doctor_ids' => '3883985,4219248']]);
        $response_cc1 = $client->request ( 'GET' ,  'test/cc1' );
        $response_cc2 = $client->request ( 'GET' ,  'test/cc2' );
//        $response_cc3 = $client->request ( 'POST' ,  'test/cc3' );
//        $response_cc4 = $client->request ( 'GET' ,  'test/cc4' );

        //$response_bb1 = $client2->request ( 'GET' ,  '/v1/test/bb1' );
        //$response_bb2 = $client2->request ( 'GET' ,  '/v1/test/bb2' );
        //$response_bb3 = $client2->request ( 'GET' ,  '/v1/test/bb3' );


        echo 'cc1-' . $response_cc1->getStatusCode () . "\r\n";
        echo 'cc2-' . $response_cc2->getStatusCode () . "\r\n";
//        echo 'cc3-' . $response_cc3->getStatusCode () . "\r\n";
//        echo 'cc4-' . $response_cc4->getStatusCode () . "\r\n";
        //echo $response_bb1->getStatusCode () . "\r\n";
        //echo $response_bb2->getStatusCode () . "\r\n";
        //echo $response_bb3->getStatusCode () . "\r\n";
        $t2 = microtime(true);
        echo $t2-$t1 . "\r\n";
        //@file_put_contents('E:\data\logs\mysql-1000-linux-222.log',$t2-$t1 . "\r\n",FILE_APPEND);
    }

    private function curl($url = '', $params = array(), $header=array(),$userAgent=false)
    {
        if (empty($url)) {
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if(!empty($params)) {
            $params = http_build_query($params, null, '&');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        if(!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        if($userAgent){
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        }
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
//        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if ($result === false) {
            $result = curl_errno($ch);
        }
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpStatusCode;
    }

    public function f()
    {
        $t1 = microtime(true);
        $cc1 = $this->curl('http://test.api.datacenter.miaoshou.com/v1/test/cc1');
        $cc2 = $this->curl('http://test.api.datacenter.miaoshou.com/v1/test/cc2');
        $cc3 = $this->curl('http://test.api.datacenter.miaoshou.com/v1/test/cc3');
        $cc4 = $this->curl('http://test.api.datacenter.miaoshou.com/v1/test/cc4');
        echo 'cc1-' . $cc1 . "\r\n";
        echo 'cc2-' . $cc2 . "\r\n";
        echo 'cc3-' . $cc3 . "\r\n";
        echo 'cc4-' . $cc4 . "\r\n";
        $t2 = microtime(true);
        echo $t2-$t1 . "\r\n";
        @file_put_contents('E:\data\logs\mysql-1000-curl-linux.log',$t2-$t1 . "\r\n",FILE_APPEND);
    }
}

$a = new HttpTest;
$a->c();

//for($i = 1; $i <= 1500; $i++){
//    if($i == 5){
//        sleep(1);
//    }
//    if($i == 100){
//        sleep(1);
//    }
//    if($i == 500){
//        sleep(2);
//    }
//    $a->c();
//}
//
//for($i = 1; $i <= 1000; $i++){
//    if($i == 5){
//        sleep(1);
//    }
//    if($i == 100){
//        sleep(1);
//    }
//    if($i == 300){
//        sleep(2);
//    }
//    $a->c2();
//}

//for($i = 1; $i <= 1000; $i++){
//    if($i == 5){
//        sleep(1);
//    }
//    if($i == 100){
//        sleep(1);
//    }
//    if($i == 300){
//        sleep(2);
//    }
//    $a->f();
//}

