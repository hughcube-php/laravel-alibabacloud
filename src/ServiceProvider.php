<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/2/23
 * Time: 11:04.
 */

namespace HughCube\Laravel\AlibabaCloud;

class ServiceProvider extends \HughCube\Laravel\ServiceSupport\ServiceProvider
{
    protected function getPackageFacadeAccessor(): string
    {
        return 'alibabaCloud';
    }

    protected function createPackageFacadeRoot($app): Manager
    {
        return new Manager();
    }
}
