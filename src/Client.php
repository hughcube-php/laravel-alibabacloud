<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/2/23
 * Time: 11:20.
 */

namespace HughCube\Laravel\AlibabaCloud;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Clients\AccessKeyClient;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use AlibabaCloud\Client\Resolver\Rpc as AlibabaCloudRpc;
use AlibabaCloud\Client\Result\Result;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @mixin AccessKeyClient
 */
class Client
{
    private static $clientIndex = 0;

    /**
     * @var AccessKeyClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array 阿里云的配置
     */
    protected $config;

    /**
     * Client constructor.
     * @param  array  $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     * @throws ClientException
     * @throws ClientException
     */
    public function getName(): string
    {
        if (empty($this->name)) {
            $this->getClient()->name($this->name = $this->genClientName());
        }
        return $this->name;
    }

    /**
     * @return AccessKeyClient
     * @throws ClientException
     */
    public function getClient(): AccessKeyClient
    {
        if (!$this->client instanceof AccessKeyClient) {
            $this->client = $this->createClient($this->config);
        }
        return $this->client;
    }

    /**
     * @param  array  $config
     * @return AccessKeyClient
     * @throws ClientException
     */
    protected function createClient(array $config): AccessKeyClient
    {
        $client = AlibabaCloud::accessKeyClient($config['AccessKeyID'], $config['AccessKeySecret']);
        $client->options($config['Options'] ?? []);

        if (!empty($config['RegionId'])) {
            $client = $client->regionId($config['RegionId']);
        }

        return $client;
    }

    /**
     * @return string
     */
    protected function genClientName(): string
    {
        $string = serialize([++self::$clientIndex, Str::random(), __METHOD__]);
        return sprintf('%s-%s', md5($string), crc32($string));
    }

    /**
     * 给request添加上client
     *
     * @param  AlibabaCloudRpc  $request
     * @return AlibabaCloudRpc
     * @throws ClientException
     */
    public function withClient(AlibabaCloudRpc $request)
    {
        return $request->client($this->getName());
    }

    /**
     * 发送请求
     *
     * @param  AlibabaCloudRpc  $request
     * @return Result
     * @throws ClientException
     * @throws ServerException
     */
    public function request(AlibabaCloudRpc $request): Result
    {
        return $this->withClient($request)->request();
    }

    /**
     * @param  null|string|integer  $key
     * @param  null|mixed  $default
     * @return mixed
     */
    public function getConfig($key = null, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * @return string|null
     */
    public function getAccessKeyId()
    {
        return Arr::get($this->getConfig(), 'AccessKeyID');
    }

    /**
     * @return string|null
     */
    public function getAccessKeySecret()
    {
        return Arr::get($this->getConfig(), 'AccessKeySecret');
    }

    /**
     * @return string|null
     */
    public function getRegionId()
    {
        return Arr::get($this->getConfig(), 'RegionId');
    }

    /**
     * @return string|null
     */
    public function getAccountId()
    {
        return Arr::get($this->getConfig(), 'AccountId');
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return Arr::get($this->getConfig(), 'Options', []);
    }

    /**
     * @return static
     */
    public function with($config): Client
    {
        $class = static::class;
        return new $class(array_merge($this->getConfig(), $config));
    }

    /**
     * 变更所在地区.
     *
     * @param  string  $regionId
     * @return static
     */
    public function withRegionId(string $regionId): Client
    {
        return $this->with(['RegionId' => $regionId]);
    }

    /**
     * 变更Options.
     *
     * @param  array  $options
     * @return static
     */
    public function withOptions(array $options): Client
    {
        return $this->with(['Options' => $options]);
    }

    /**
     * @param  string  $name
     * @param  array  $arguments
     * @return mixed
     * @throws ClientException
     */
    public function __call(string $name, array $arguments = [])
    {
        return $this->getClient()->$name(...$arguments);
    }
}
