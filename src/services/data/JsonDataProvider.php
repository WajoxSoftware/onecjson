<?php
namespace wajox\onecjson\services\data;

use GuzzleHttp\Client;

class JsonDataProvider extends \yii\base\Object
{
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

	public function __construct(array $params = [])
	{
		$this->setHost($params['host'])
			 ->setUser($params['user'])
			 ->setPassword($params['password'])
			 ->setEnableCache($params['enableCache'])
			 ->setCacheTime($params['cacheTime'])
			 ->setCachePrefix($params['cachePrefix']);
	}

	public function count(
		string $path,
		array $query = [],
		array $params = []
	): int
	{
		$queryString = http_build_query($query, '', '&', PHP_QUERY_RFC3986);

		$params['auth'] = [
			$this->getUser(),
			$this->getPassword(),
		];

		$response = $this->getClient()->request(
			self::METHOD_GET,
			$path . '/$count/?' . $queryString,
			$params
		);

		return (int) json_decode($response->getBody());
	}

	public function get(string $path, array $query = [], bool $cache = true): array
	{
		return $this->request(self::METHOD_GET, $path, $query, [], $cache);
	}

	public function post(string $path, array $query = [], array $params = [], bool $cache = false): array
	{
		return $this->request(self::METHOD_POST, $path, $query, $params, $cache);
	}

	public function patch(string $path, array $query = [], array $params = [])
	{
		return $this->request(self::METHOD_PATCH, $path, $query, $params, false);
	}

	public function put(string $path, array $query = [], array $params = []): array
	{
		return $this->request(self::METHOD_PUT, $path, $query, $params, false);
	}
	
	public function delete(string $path, array $query = []): array
	{
		return $this->request(self::METHOD_DELETE, $path, $query, false);
	}

	protected function setHost(string $host): JsonDataProvider
	{
		$this->host = $host;

		return $this;
	}

	protected function getHost(): string
	{
		return $this->host;
	}

	protected function setUser(string $user): JsonDataProvider
	{
		$this->user = $user;

		return $this;
	}

	protected function getUser(): string
	{
		return $this->user;
	}

	protected function setPassword(string $password): JsonDataProvider
	{
		$this->password = $password;

		return $this;
	}

	protected function getPassword(): string
	{
		return $this->password;
	}

	public function getClient(): Client
	{
		return new Client([
			'base_uri' => $this->getHost(),
		]);
	}

	protected function request(
		string $method,
		string $path,
		array $query = [],
		array $params = [],
		bool $cache = false
	): array
	{
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
		string $method,
		string $path,
		array $query = [],
		array $params = []
	): array
	{
		$key = $this->getCache()->buildKey([
			$this->getCachePrefix(),
			$method,
			$path,
			$query
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
		string $method,
		string $path,
		array $query = [],
		array $params = []
	): array
	{
		$query['$format'] = self::FORMAT_JSON;

		$queryString = http_build_query($query, '', '&', PHP_QUERY_RFC3986);

		$params['auth'] = [
			$this->getUser(),
			$this->getPassword(),
		];

		$response = $this->getClient()->request(
			$method,
			$path . '/?' . $queryString,
			['form_params' => $params]
		);

		return json_decode($response->getBody(), true);
	}

	protected function setEnableCache(bool $enableCache): JsonDataProvider
	{
		$this->enableCache = $enableCache;

		return $this;
	}

	protected function isCacheEnabled(): bool
	{
		return $this->enableCache;
	}

	protected function setCacheTime(int $cacheTime): JsonDataProvider
	{
		$this->cacheTime = $cacheTime;

		return $this;
	}

	protected function getCacheTime(): int
	{
		return $this->cacheTime;
	}

	protected function getCache(): \yii\caching\Cache
	{
		return \Yii::$app->cache;
	}

	protected function setCachePrefix(string $cachePrefix): JsonDataProvider
	{
		$this->cachePrefix = $cachePrefix;

		return $this;
	}

	protected function getCachePrefix(): string
	{
		return $this->cachePrefix;
	}
}
