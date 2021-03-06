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
 * @method static Client client(string $name = null)
 * @method static Client makeClient(array $config)
 * @method static Client makeClientFromEnv($idName = null, $secretName = null, $regionName = null, $accountName = null)
 */
class AlibabaCloud extends IlluminateFacade
{
    const KEY_ID_ENV_NAME = 'ALIYUN_ACCESS_KEY';
    const KEY_SECRET_ENV_NAME = 'ALIYUN_ACCESS_KEY_SECRET';
    const REGION_ENV_NAME = 'ALIYUN_REGION';
    const ACCOUNT_ENV_NAME = 'ALIYUN_ACCOUNT_ID';

    /**
     * @var AlibabaCloudSdk
     */
    private static $sdk;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'alibabaCloud';
    }

    /**
     * @return AlibabaCloudSdk
     */
    public static function sdk()
    {
        if (! static::$sdk instanceof AlibabaCloudSdk) {
            static::$sdk = new AlibabaCloudSdk();
        }

        return static::$sdk;
    }
}
