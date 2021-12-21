<?php
namespace Ass\request\http;
use Ass\tools\Format;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Exception;
use GuzzleHttp\RedirectMiddleware;
use GuzzleHttp\Psr7\Utils;

/**
 * kly
 * php http 请求封装
*/
class General
{

    /**
     * @var mixed Default client
     */
    private $client;

    /**
     * @var mixed Default request
     */
    private $request;

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
     * @param string $url
     * @param array $query
     * @return mixed
    */
    public function getQuery($url = '',$query = [])
    {
        $this->request = [
            'method' => 'GET',
            'url' => $url,
            'zoom' => ['query' => $query]
        ];
        return new GeneralRequest($this->client,$this->request);
    }

    /**
     * kly
     * 构建 POST 表单域请求
     * application/x-www-form-urlencoded
     * @param string $url
     * @param array $data
     * @return mixed
     */
    public function postFormUrlencoded($url = '',$data = [])
    {
        $this->request = [
            'method' => 'POST',
            'url' => $url,
            'zoom' => ['form_params' => $data]
        ];
        return new GeneralRequest($this->client,$this->request);
    }

    /**
     * kly
     * 构建 POST 表单域,支持文件域
     * multipart/form-data
     * @param string $url
     * @param array $data
     * @param array $file [[key => value]] 支持多文件
     * @return mixed
     */
    public function postFormData($url = '',$data = [],$file = [])
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
        $this->request = [
            'method' => 'POST',
            'url' => $url,
            'zoom' => ['multipart' => $postData]
        ];
        return new GeneralRequest($this->client,$this->request);
    }

    /**
     * kly
     * GET HTTP AUTH 认证
     * 支持 basic | digest | ntlm
     * PHP 依赖 Apache Nginx 仅支持 前两种
     * @param string $url
     * @param array $auth ['username', 'password']
     * @param string $type basic | digest | ntlm
     * @return mixed
     */
    public function getAuth($url = '',$auth = [],$type = 'basic')
    {
        array_push($auth,$type);
        $this->request = [
            'method' => 'GET',
            'url' => $url,
            'zoom' => ['auth' => $auth]
        ];
        return new GeneralRequest($this->client,$this->request);

    }

    /**
     * kly
     * POST JSON 数据
     * @param string $url
     * @param array $data
     * @return mixed
     */
    public function postJson($url = '',$data = [])
    {
        $this->request = [
            'method' => 'POST',
            'url' => $url,
            'zoom' => ['json' => json_encode($data)]
        ];
        return new GeneralRequest($this->client,$this->request);
    }

    /**
     * kly
     * POST 上传 数据
     * @param string $url
     * @param array $file [[key => value]] 支持多文件
     * @return mixed
     */
    public function postUpload($url = '',$file = [])
    {
        $postData = [];
        if($file){
            foreach ($file as $name => $contents)
                $postData[] = [
                    'name' => $name,
                    'contents' => Utils::tryFopen($contents, 'r'),
                ];
        }
        $this->request = [
            'method' => 'POST',
            'url' => $url,
            'zoom' => ['multipart' => $postData]
        ];
        return new GeneralRequest($this->client,$this->request);
    }
}

class GeneralRequest
{
    /**
     * @var mixed Default client
     */
    private $client;

    /**
     * @var mixed Default request
     */
    private $request;

    /**
     * kly
     * 初始化操作
     * @param mixed $client
     * @param array $request
     */
    public function __construct($client,$request)
    {
        $this->client = $client;
        $this->request = $request;
    }

    /**
     * kly
     * 同步发送请求
     */
    public function send()
    {
        try{
            $response = $this->client->request($this->request['method'],$this->request['url'],$this->request['zoom']);
            $body = $response->getBody();
            if(!Format::isLegalJson($body)){
                return ['code' => 110,'msg' => 'Invalid JSON data ','data' => strval($body)];
            }
            return json_decode($response->getBody(),true);
        } catch (Exception $e){
            return ['code' => $e->getCode(),'msg' => $e->getMessage(),'data' => []];
        }
    }
}