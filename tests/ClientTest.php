<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/6/15
 * Time: 7:14 下午.
 */

namespace HughCube\Laravel\AlibabaCloud\Tests;

use AlibabaCloud\Client\AlibabaCloud as AliYunAlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use HughCube\Laravel\AlibabaCloud\AlibabaCloud;
use HughCube\Laravel\AlibabaCloud\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ClientTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(Client::class, AlibabaCloud::client());
    }

    /**
     * @return void
     *
     * @throws ClientException
     */
    public function testClient()
    {
        $this->assertClient(
            AlibabaCloud::client(),
            config(sprintf('alibabaCloud.clients.%s', config('alibabaCloud.default')))
        );

        foreach (config('alibabaCloud.clients') as $name => $value) {
            $this->assertClient(AlibabaCloud::client($name), $value);
        }
    }

    /**
     * @throws ClientException
     */
    protected function assertClient(Client $client, $config)
    {
        $this->assertSame(Arr::get($config, 'AccessKeyID'), $client->getAccessKeyId());
        $this->assertSame(Arr::get($config, 'AccessKeySecret'), $client->getAccessKeySecret());
        $this->assertSame(Arr::get($config, 'RegionId'), $client->getRegionId());
        $this->assertSame(Arr::get($config, 'AccountId'), $client->getAccountId());
        $this->assertSame(Arr::get($config, 'Options'), $client->getOptions());

        $this->assertNotEmpty($client->getName());
        $this->assertSame(AliYunAlibabaCloud::get($client->getName()), $client->getClient());

        $request = AlibabaCloud::sdk()::domain()::v20180208()->BidDomain();
        $this->assertSame($request, $client->withClient($request));
        $this->assertSame($request->httpClient(), $client->getClient());

        $client->asDefaultClient();
        $this->assertSame(AliYunAlibabaCloud::getDefaultClient(), $client->getClient());

        $regionId = md5(Str::random(100));
        $this->assertSame($client->withRegionId($regionId)->getRegionId(), $regionId);

        $options = [md5(Str::random(100))];
        $this->assertSame($client->withOptions($options)->getOptions(), $options);
    }
}
