<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/2/23
 * Time: 10:58.
 */

namespace HughCube\Laravel\AlibabaCloud;

use AlibabaCloud\Client\AlibabaCloud as AlibabaCloudSdk;
use Illuminate\Support\Facades\Facade as IlluminateFacade;

/**
 * Class AlibabaCloud.
 *
 * @method static Client client(string $name = null)
 * @method static Client makeClient(array $config)
 */
class AlibabaCloud extends IlluminateFacade
{
    /**
     * @var AlibabaCloudSdk
     */
    protected static $sdk;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return 'alibabaCloud';
    }

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
}
