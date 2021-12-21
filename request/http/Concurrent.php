<?php
namespace Ass\request\http;
use Ass\tools\Format;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Exception;
use GuzzleHttp\RedirectMiddleware;
use GuzzleHttp\Psr7\Utils;
use \GuzzleHttp\Promise\Utils as PU;
use Throwable;

/**
 * kly
 * php http 请求封装
*/
class Concurrent
{

    /**
     * @var mixed Default client
     */
    private $client;

    /**
     * kly
     * 初始化操作
     * @param string $base_uri 域名
     * @param array $configure 配置项数组,参见默认配置项 defaultConfigure
    */
    public function __construct($base_uri = '',$configure = [])
    {
        $defCon = $this->defaultConfigure();
        if($base_uri) $defCon['base_uri'] = trim($base_uri);
        $con = array_merge($defCon,$configure);
        if($con['cookies']) $con['cookies'] = CookieJar::fromArray($con['cookies'],parse_url($base_uri)['host']);
        $this->client = new Client($con);
    }

    /**
     * kly
     * 默认配置
     * 支持携带 cookies
    */
    private function defaultConfigure()
    {
        return [
            //重定向
            'allow_redirects' => RedirectMiddleware::$defaultSettings,
            //HTTP错误是否抛出异常
            'http_errors'     => true,
            //是否自动解码
            'decode_content'  => true,
            //SSL 验证
            'verify'          => false,
            //是否携带cookies array
            'cookies'         => false,
            //IDN支持
            'idn_conversion'  => false,
            //超时时间,单位秒
            'timeout' => 5,
            //请求头，伪装一下喽
            'headers' => [
                'User-Agent' => 'Mozilla/5.0'
            ],
        ];
    }
    
    /**
     * kly
     * 构建 GET请求
     * @param string $name 自定义请求名
     * @param string $url
     * @param array $query
     * @return array
    */
    public function getQuery($name,$url = '',$query = [])
    {
        return  [
            'method' => 'GET',
            'url' => $url,
            'zoom' => ['query' => $query],
            'client' => $this->client,
            'name' => $name
        ];

    }

    /**
     * kly
     * 构建 POST 表单域请求
     * application/x-www-form-urlencoded
     * @param string $name 自定义请求名
     * @param string $url
     * @param array $data
     * @return array
     */
    public function postFormUrlencoded($name,$url = '',$data = [])
    {
        return  [
            'method' => 'POST',
            'url' => $url,
            'zoom' => ['form_params' => $data],
            'client' => $this->client,
            'name' => $name
        ];
    }

    /**
     * kly
     * 构建 POST 表单域,支持文件域
     * multipart/form-data
     * @param string $name 自定义请求名
     * @param string $url
     * @param array $data
     * @param array $file [[key => value]] 支持多文件
     * @return array
     */
    public function postFormData($name,$url = '',$data = [],$file = [])
    {
        $postData = [];
        if($data){
            foreach ($data as $name => $contents){
                $postData[] = [
                    'name' => $name,
                    'contents' => $contents
                ];
            }
        }
        if($file){
            foreach ($file as $name => $contents)
                $postData[] = [
                    'name' => $name,
                    'contents' => Utils::tryFopen($contents, 'r'),
                ];
        }
        return  [
            'method' => 'POST',
            'url' => $url,
            'zoom' => ['multipart' => $postData],
            'client' => $this->client,
            'name' => $name
        ];
    }

    /**
     * kly
     * GET HTTP AUTH 认证
     * 支持 basic | digest | ntlm
     * PHP 依赖 Apache Nginx 仅支持 前两种
     * @param string $name 自定义请求名
     * @param string $url
     * @param array $auth ['username', 'password']
     * @param string $type basic | digest | ntlm
     * @return array
     */
    public function getAuth($name,$url = '',$auth = [],$type = 'basic')
    {
        array_push($auth,$type);
        return  [
            'method' => 'GET',
            'url' => $url,
            'zoom' => ['auth' => $auth],
            'client' => $this->client,
            'name' => $name
        ];

    }

    /**
     * kly
     * POST JSON 数据
     * @param string $name 自定义请求名
     * @param string $url
     * @param array $data
     * @return array
     */
    public function postJson($name,$url = '',$data = [])
    {
        return  [
            'method' => 'POST',
            'url' => $url,
            'zoom' => ['json' => json_encode($data)],
            'client' => $this->client,
            'name' => $name
        ];
    }

    /**
     * kly
     * POST 上传 数据
     * @param string $name 自定义请求名
     * @param string $url
     * @param array $file [[key => value]] 支持多文件
     * @return mixed
     */
    public function postUpload($name,$url = '',$file = [])
    {
        $postData = [];
        if($file){
            foreach ($file as $name => $contents)
                $postData[] = [
                    'name' => $name,
                    'contents' => Utils::tryFopen($contents, 'r'),
                ];
        }
        return  [
            'method' => 'POST',
            'url' => $url,
            'zoom' => ['multipart' => $postData],
            'client' => $this->client,
            'name' => $name
        ];
    }
}

/**
 * kly
 * 发送类
*/
class ConcurrentRequest
{
    /**
     * kly
     * 并发请求，两个以上请求
     * 对单接口响应时间较长的支持效果比较好，
     * 若调用的接口响应比较及时，可不必使用此法
     * 支持 GET POST 混合模式
     * @param $param
     * @return array
     */
    public function send(...$param)
    {
        if(func_num_args() < 2) return ['code' => 111,'msg' => 'The send method requires two or more parameters ','data' => []];
        $promises = [];
        $data = [];
        foreach (func_get_args() as $arg) {
            $promises[$arg['name']] = $arg['client']->requestAsync ($arg['method'] ,$arg['url'],$arg['zoom']);
        }
        try{
            $responses = PU::unwrap($promises);
            foreach (array_keys($responses) as $name){
                $body = $responses[$name]->getBody();
                $data[$name] = (!Format::isLegalJson($body)) ?
                    ['code' => 110,'msg' => 'Invalid JSON data ','data' => strval($body)] :
                    json_decode($body,true);
            }
            return $data;
        } catch (Exception $e) {
            return ['code' => $e->getCode(),'msg' => $e->getMessage(),'data' => []];
        } catch (Throwable $e) {
            return ['code' => $e->getCode(),'msg' => $e->getMessage(),'data' => []];
        }
    }
}
