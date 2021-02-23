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

class Client
{
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
     * 获取客户端名称
     *
     * @return string
     */
    public function getName(): string
    {
        if (empty($this->name)) {
            $this->name = md5(serialize([__METHOD__, $this->config, rand(1, 99999999), microtime()]));

            $this->client = AlibabaCloud::accessKeyClient(
                $this->config["accessKey"],
                $this->config["accessKeySecret"]
            );
            if (!empty($this->config["regionId"])) {
                $this->client->regionId($this->config["regionId"]);
            }
            $this->client->name($this->name);
        }

        return $this->name;
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
}
