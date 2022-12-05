<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/2/23
 * Time: 10:58.
 */

namespace HughCube\Laravel\AlibabaCloud;

use AlibabaCloud\Client\AlibabaCloud as AlibabaCloudSdk;
use HughCube\Laravel\ServiceSupport\LazyFacade;

/**
 * Class AlibabaCloud.
 *
 * @method static Client client(string $name = null)
 * @method static Client makeClient(array $config)
 */
class AlibabaCloud extends LazyFacade
{
    /**
     * @var AlibabaCloudSdk
     */
    protected static $sdk;

    /**
     * @return AlibabaCloudSdk
     */
    public static function sdk(): AlibabaCloudSdk
    {
        if (! static::$sdk instanceof AlibabaCloudSdk) {
            static::$sdk = new AlibabaCloudSdk();
        }

        return static::$sdk;
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return 'alibabaCloud';
    }

    protected static function registerServiceProvider($app)
    {
        $app->register(ServiceProvider::class);
    }
}
