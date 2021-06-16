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
use AlibabaCloud\Client\Resolver\Rpc as AlibabaCloudRpc;
use Illuminate\Support\Arr;

class Client
{
    private static $clientIndex = 0;

    /**
     * @var string Client对应的名称
     */
    protected $name;

    /**
     * @var AccessKeyClient
     */
    protected $client;

    /**
     * @var array 阿里云的配置
     */
    protected $config;

    /**
     * Client constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $this->formatConfig($config);
    }

    /**
     * @param array $config
     * @return array
     */
    protected function formatConfig(array $config)
    {
        return [
            'AccessKeyID' => $this->getOneValue(['AccessKeyID', 'accessKeyID', 'accessKey', 'AccessKey'], $config),
            'AccessKeySecret' => $this->getOneValue(['AccessKeySecret', 'accessKeySecret'], $config),
            'RegionId' => $this->getOneValue(['RegionId', 'regionId'], $config),
            'AccountId' => $this->getOneValue(['AccountId', 'accountId'], $config),
            'Options' => $this->getOneValue(['Options', 'options'], $config, []),
        ];
    }

    /**
     * @param array $keys
     * @param array $array
     * @return array|\ArrayAccess|mixed|null
     */
    protected function getOneValue($keys, $array, $default = null)
    {
        foreach ($keys as $key) {
            if (Arr::has($array, $key)) {
                return Arr::get($array, $key);
            }
        }

        return $default;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if (empty($this->name)) {
            $this->client = $client = $this->createAlibabaCloudClient();
            $client->name(($this->name = $this->randomName()));
        }

        return $this->name;
    }

    /**
     * @return AccessKeyClient
     */
    public function getClient()
    {
        $this->getName();

        return $this->client;
    }

    /**
     * @return AccessKeyClient
     * @throws \AlibabaCloud\Client\Exception\ClientException
     */
    protected function createAlibabaCloudClient()
    {
        $client = AlibabaCloud::accessKeyClient($this->getAccessKeyId(), $this->getAccessKeySecret());

        $client->options($this->getOptions());
        null !== ($regionId = $this->getRegionId()) and $client->regionId($regionId);

        return $client;
    }

    /**
     * @return string
     */
    protected function randomName()
    {
        $string = serialize([__METHOD__, $this->config, microtime(), ++self::$clientIndex, random_int(0, 9999999999)]);

        return sprintf('%s-%s', md5($string), crc32($string));
    }

    /**
     * 把当前实例设置为默认实例.
     *
     * @return $this
     * @throws \AlibabaCloud\Client\Exception\ClientException
     */
    public function asDefault()
    {
        $this->client->asDefaultClient();

        return $this;
    }

    /**
     * 给request添加上client.
     *
     * @param AlibabaCloudRpc $request
     * @return AlibabaCloudRpc
     */
    public function withClient(AlibabaCloudRpc $request)
    {
        return $request->client($this->getName());
    }

    /**
     * 发送请求
     *
     * @param AlibabaCloudRpc $request
     * @return \AlibabaCloud\Client\Result\Result
     */
    public function request(AlibabaCloudRpc $request)
    {
        return $this->withClient($request)->request();
    }

    /**
     * @param null|string $key
     * @param null|mixed $default
     * @return array|\ArrayAccess|mixed
     */
    public function getConfig($key = null, $default = null)
    {
        if (null === $key) {
            return $this->config;
        }

        return Arr::get($this->config, $key, $default);
    }

    /**
     * @return string|null
     */
    public function getAccessKeyId()
    {
        return Arr::get($this->config, 'AccessKeyID');
    }

    /**
     * @return string|null
     */
    public function getAccessKeySecret()
    {
        return Arr::get($this->config, 'AccessKeySecret');
    }

    /**
     * @return string|null
     */
    public function getRegionId()
    {
        return Arr::get($this->config, 'RegionId');
    }

    /**
     * @return string|null
     */
    public function getAccountId()
    {
        return Arr::get($this->config, 'AccountId');
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return Arr::get($this->config, 'Options', []);
    }

    /**
     * @return static
     */
    public function with($config)
    {
        $class = static::class;

        return new $class(array_merge($this->config, $config));
    }

    /**
     * 变更所在地区.
     *
     * @param string $regionId
     * @return static
     */
    public function withRegionId($regionId)
    {
        return $this->with(['AccountId' => $regionId]);
    }

    /**
     * 变更Options.
     *
     * @param array $options
     * @return static
     */
    public function withOptions(array $options)
    {
        return $this->with(['Options' => $options]);
    }
}
