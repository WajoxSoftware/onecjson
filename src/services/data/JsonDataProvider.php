<?php
namespace wajox\onec\services\data;

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

	public function __construct(array $params = [])
	{
		$this->setHost($params['host'])
			 ->setUser($params['user'])
			 ->setPassword($params['password']);
	}

	public function count(
		string $path,
		array $query = [],
		array $params = []
	): int
	{
		$queryString = http_build_query($query);

		$params['auth'] = [
			$this->getUser(),
			$this->getPassword(),
		];

		$response = $this->getClient()->request(
			self::METHOD_GET,
			$path . '/$count/?' . $queryString,
			$params
		);

		return (int) $response->getBody();
	}

	public function get(string $path, array $query = []): array
	{
		return $this->request(self::METHOD_GET, $path, $query);
	}

	public function post(string $path, array $query = [], array $params = []): array
	{
		return $this->request(self::METHOD_POST, $path, $query, $params);
	}

	public function patch(string $path, array $query = [], array $params = [])
	{
		return $this->request(self::METHOD_PATCH, $path, $query, $params);
	}

	public function put(string $path, array $query = [], array $params = []): array
	{
		return $this->request(self::METHOD_PUT, $path, $query, $params);
	}
	
	public function delete(string $path, array $query = []): array
	{
		return $this->request(self::METHOD_DELETE, $path, $query);
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

	protected function getClient(): Client
	{
		return new Client([
			'base_uri' => $this->getHost(),
		]);
	}

	protected function request(
		string $method,
		string $path,
		array $query = [],
		array $params = []
	): array
	{
		$query['$format'] = self::FORMAT_JSON;

		$queryString = http_build_query($query);

		$params['auth'] = [
			$this->getUser(),
			$this->getPassword(),
		];

		$response = $this->getClient()->request(
			$method,
			$path . '/?' . $queryString,
			$params
		);

		return json_decode($response->getBody(), true);
	}
}
