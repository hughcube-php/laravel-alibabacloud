<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/6/15
 * Time: 7:14 下午.
 */

namespace HughCube\Laravel\AlibabaCloud\Tests;

use AlibabaCloud\Client\AlibabaCloud as AlibabaCloudSdk;
use HughCube\Laravel\AlibabaCloud\AlibabaCloud;
use HughCube\Laravel\AlibabaCloud\Client;

class FacadeTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(Client::class, AlibabaCloud::client());
    }

    public function testSdk()
    {
        $this->assertInstanceOf(AlibabaCloudSdk::class, AlibabaCloud::sdk());
        $this->assertNotEmpty(AlibabaCloud::sdk()::VERSION);
    }
}
