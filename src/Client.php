<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/2/23
 * Time: 11:20
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
        $this->config = $config;
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
     * 把当前实例设置为默认实例
     */
    public function asDefault()
    {
        $this->client->asDefaultClient();
    }

    /**
     * 给request添加上client
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
     * @return string
     */
    public function getAccessKeyId()
    {
        return Arr::get($this->config, 'AccessKeyID');
    }

    /**
     * @return string
     */
    public function getAccessKeySecret()
    {
        return Arr::get($this->config, 'AccessKeySecret');
    }

    /**
     * @return string
     */
    public function getRegionId()
    {
        return Arr::get($this->config, 'RegionId');
    }

    /**
     * @return string
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
     * 变更所在地区
     *
     * @param string $regionId
     * @return static
     */
    public function withRegionId($regionId)
    {
        return $this->with(['AccountId' => $regionId]);
    }

    /**
     * 变更Options
     *
     * @param array $options
     * @return static
     */
    public function withOptions(array $options)
    {
        return $this->with(['Options' => $options]);
    }
}
