<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/20
 * Time: 11:36 下午.
 */

namespace HughCube\Laravel\AlibabaCloud\Tests;

use HughCube\Laravel\AlibabaCloud\AlibabaCloud;
use HughCube\Laravel\AlibabaCloud\ServiceProvider;
use Illuminate\Auth\Passwords\PasswordResetServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * @inheritDoc
     */
    protected function getApplicationProviders($app)
    {
        $providers = parent::getApplicationProviders($app);

        unset($providers[array_search(PasswordResetServiceProvider::class, $providers)]);

        return $providers;
    }

    /**
     * @inheritDoc
     */
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('alibabaCloud', (require __DIR__.'/config//alibabaCloud.php'));

        foreach (
            [
                AlibabaCloud::KEY_ID_ENV_NAME => md5(random_bytes(100)),
                AlibabaCloud::KEY_SECRET_ENV_NAME => md5(random_bytes(100)),
                AlibabaCloud::REGION_ENV_NAME => md5(random_bytes(100)),
                AlibabaCloud::ACCOUNT_ENV_NAME => md5(random_bytes(100)),
            ] as $name => $value
        ) {
            putenv("$name=$value");
        }
    }
}
