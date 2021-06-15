<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/6/15
 * Time: 7:14 下午
 */

namespace HughCube\Laravel\AlibabaCloud\Tests;

use HughCube\Laravel\AlibabaCloud\AlibabaCloud;
use HughCube\Laravel\AlibabaCloud\Client;
use Illuminate\Support\Arr;

class ClientTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(Client::class, AlibabaCloud::client());
    }

    public function testClient()
    {
        $this->assertClient(
            AlibabaCloud::client(),
            config(sprintf('alibabaCloud.clients.%s', config('alibabaCloud.default')))
        );

        foreach (config('alibabaCloud.clients') as $name => $value) {
            $this->assertClient(AlibabaCloud::client($name), $value);
        }

        $this->assertClient(AlibabaCloud::makeClientFromEnv(), [
            "AccessKeyID" => env(AlibabaCloud::ACCESS_KEY_ID_ENV_NAME),
            "AccessKeySecret" => env(AlibabaCloud::ACCESS_KEY_SECRET_ENV_NAME),
            "RegionId" => env(AlibabaCloud::REGION_ID_ENV_NAME),
            "AccountId" => env(AlibabaCloud::ACCOUNT_ID_ENV_NAME),
            "Options" => []
        ]);
    }

    protected function assertClient(Client $client, $config)
    {
        $this->assertSame(Arr::get($config, 'AccessKeyID'), $client->getAccessKeyId());
        $this->assertSame(Arr::get($config, 'AccessKeySecret'), $client->getAccessKeySecret());
        $this->assertSame(Arr::get($config, 'RegionId'), $client->getRegionId());
        $this->assertSame(Arr::get($config, 'AccountId'), $client->getAccountId());
        $this->assertSame(Arr::get($config, 'Options'), $client->getOptions());
        $this->assertNotEmpty($client->getName());
    }
}
