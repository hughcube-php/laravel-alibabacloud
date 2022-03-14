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
    protected function getApplicationProviders($app): array
    {
        $providers = parent::getApplicationProviders($app);

        unset($providers[array_search(PasswordResetServiceProvider::class, $providers)]);

        return $providers;
    }

    /**
     * @inheritDoc
     */
    protected function getPackageProviders($app): array
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
    }
}
