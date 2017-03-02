<?php
namespace wajox\onecjson\services\data;

use GuzzleHttp\Client;

class JsonDataProvider extends \yii\base\Object
{
    const AUTH_TYPE_BASIC = 'basic';
    const AUTH_TYPE_NTLM = 'ntlm';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';

    const FORMAT_JSON = 'json';

    protected $host;
    protected $user;
    protected $password;
    protected $enableCache;
    protected $cacheTime = null;
    protected $cachePrefix = '';
    protected $authType = null;

    public function __construct($params = [])
    {
        $this->setHost($params['host'])
             ->setUser($params['user'])
             ->setPassword($params['password'])
             ->setEnableCache($params['enableCache'])
             ->setCacheTime($params['cacheTime'])
             ->setCachePrefix($params['cachePrefix'])
             ->setAuthType($params['authType']);
    }

    public function count(
        $path,
        $query = [],
        $params = []
    ) {
        $queryString = http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        $params = $this->addAuthParams($params);

        $response = $this->getClient()->request(
            self::METHOD_GET,
            $path . '/$count/?' . $queryString,
            $params
        );

        return (int) json_decode($response->getBody());
    }

    public function get($path, $query = [], $cache = true)
    {
        return $this->request(self::METHOD_GET, $path, $query, [], $cache);
    }

    public function post($path, $query = [], $params = [], $cache = false)
    {
        return $this->request(self::METHOD_POST, $path, $query, $params, $cache);
    }

    public function patch($path, $query = [], $params = [])
    {
        return $this->request(self::METHOD_PATCH, $path, $query, $params, false);
    }

    public function put($path, $query = [], $params = [])
    {
        return $this->request(self::METHOD_PUT, $path, $query, $params, false);
    }
    
    public function delete($path, $query = [])
    {
        return $this->request(self::METHOD_DELETE, $path, $query, false);
    }

    protected function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    protected function getHost()
    {
        return $this->host;
    }

    protected function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    protected function getUser()
    {
        return $this->user;
    }

    protected function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    protected function getPassword()
    {
        return $this->password;
    }

    public function getClient()
    {
        return new Client([
            'base_uri' => $this->getHost(),
        ]);
    }

    protected function setAuthType($authType)
    {
        $this->authType = $authType;

        return $this;
    }

    protected function getAuthType()
    {
        return $this->authType;
    }

    protected function isBasicAuth()
    {
        return $this->authType == self::AUTH_TYPE_BASIC;
    }

    protected function isNtlmAuth()
    {
        return $this->authType == self::AUTH_TYPE_NTLM;
    }

    protected function request(
        $method,
        $path,
        $query = [],
        $params = [],
        $cache = false
    ) {
        if ($cache
            && $this->isCacheEnabled()
        ) {
            return $this->requestCachedJson(
                $method,
                $path,
                $query,
                $params
            );
        }

        return $this->requestJson(
            $method,
            $path,
            $query,
            $params
        );
    }

    protected function requestCachedJson(
        $method,
        $path,
        $query = [],
        $params = []
    ) {
        $key = $this->getCache()->buildKey([
            $this->getCachePrefix(),
            $method,
            $path,
            $query,
            $params
        ]);

        if (!$this->getCache()->exists($key)) {
            $this->getCache()->set(
                $key,
                $this->requestJson($method, $path, $query, $params),
                $this->getCacheTime()
            );
        }

        return $this->getCache()->get($key);
    }

    protected function requestJson(
        $method,
        $path,
        $query = [],
        $params = []
    ) {
        $query['$format'] = self::FORMAT_JSON;

        $queryString = http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        $params = $this->addAuthParams($params);

        $response = $this->getClient()->request(
            $method,
            $path . '/?' . $queryString,
            $params
        );

        return (array) json_decode($response->getBody(), true);
    }

    protected function setEnableCache($enableCache)
    {
        $this->enableCache = $enableCache;

        return $this;
    }

    protected function isCacheEnabled()
    {
        return $this->enableCache;
    }

    protected function setCacheTime($cacheTime)
    {
        $this->cacheTime = $cacheTime;

        return $this;
    }

    protected function getCacheTime()
    {
        return $this->cacheTime;
    }

    protected function getCache()
    {
        return \Yii::$app->cache;
    }

    protected function setCachePrefix($cachePrefix)
    {
        $this->cachePrefix = $cachePrefix;

        return $this;
    }

    protected function getCachePrefix()
    {
        return $this->cachePrefix;
    }

    protected function addAuthParams($params)
    {
        if ($this->isBasicAuth()) {
            $params['auth'] = [
                $this->getUser(),
                $this->getPassword(),
            ];
        }

        if ($this->isNtlmAuth()) {
            $authParams = [
                $this->getUser(),
                $this->getPassword(),
            ];

            $params['curl'] = [
                CURLOPT_HTTPAUTH => CURLAUTH_NTLM,
                CURLOPT_USERPWD  => implode(':', $authParams),
            ];
        }

        return $params;
    }
}
