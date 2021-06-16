<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/6/15
 * Time: 7:14 下午.
 */

namespace HughCube\Laravel\AlibabaCloud\Tests;

use AlibabaCloud\Client\AlibabaCloud as AliYunAlibabaCloud;
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
            'AccessKeyID' => env(AlibabaCloud::KEY_ID_ENV_NAME),
            'AccessKeySecret' => env(AlibabaCloud::KEY_SECRET_ENV_NAME),
            'RegionId' => env(AlibabaCloud::REGION_ENV_NAME),
            'AccountId' => env(AlibabaCloud::ACCOUNT_ENV_NAME),
            'Options' => [],
        ]);
    }

    protected function assertClient(Client $client, $config)
    {
        if (Arr::has($config, 'AccessKeyID')) {
            $this->assertSame(Arr::get($config, 'AccessKeyID'), $client->getAccessKeyId());
            $this->assertSame(Arr::get($config, 'AccessKeySecret'), $client->getAccessKeySecret());
            $this->assertSame(Arr::get($config, 'RegionId'), $client->getRegionId());
            $this->assertSame(Arr::get($config, 'AccountId'), $client->getAccountId());
            $this->assertSame(Arr::get($config, 'Options'), $client->getOptions());
        } else {
            $this->assertSame(Arr::get($config, 'accessKey'), $client->getAccessKeyId());
            $this->assertSame(Arr::get($config, 'accessKeySecret'), $client->getAccessKeySecret());
            $this->assertSame(Arr::get($config, 'regionId'), $client->getRegionId());
            $this->assertSame(Arr::get($config, 'accountId'), $client->getAccountId());
        }

        $this->assertNotEmpty($client->getName());
        $this->assertSame(AliYunAlibabaCloud::get($client->getName()), $client->getClient());

        $request = $client->withClient(AlibabaCloud::sdk()::domain()::v20180208()->BidDomain());
        $this->assertSame($request->httpClient(), $client->getClient());

        $client->asDefault();
        $this->assertSame(AliYunAlibabaCloud::getDefaultClient(), $client->getClient());

        $regionId = md5(random_bytes(100));
        $this->assertSame($client->withRegionId($regionId)->getRegionId(), $regionId);

        $options = [md5(random_bytes(100))];
        $this->assertSame($client->withOptions($options)->getOptions(), $options);
    }
}
