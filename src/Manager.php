<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/2/23
 * Time: 11:21.
 */

namespace HughCube\Laravel\AlibabaCloud;

use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Manager as IlluminateManager;
use InvalidArgumentException;

/**
 * @property callable|ContainerContract|null $container
 */
class Manager extends IlluminateManager
{
    /**
     * @param  callable|ContainerContract|null  $container
     */
    public function __construct($container = null)
    {
        $this->container = $container;
    }

    /**
     * @return IlluminateContainer
     */
    public function getContainer(): ContainerContract
    {
        if (is_callable($this->container)) {
            $this->container = call_user_func($this->container);
        } elseif (null === $this->container) {
            $this->container = IlluminateContainer::getInstance();
        }

        return $this->container;
    }

    /**
     * @return Repository
     *
     * @throws
     * @phpstan-ignore-next-line
     */
    protected function getConfig(): Repository
    {
        if (! $this->config instanceof Repository) {
            $this->config = $this->getContainer()->make('config');
        }

        return $this->config;
    }

    /**
     * @param  null|string|int  $name
     * @param  mixed  $default
     * @return array|mixed
     */
    protected function getPackageConfig($name = null, $default = null)
    {
        $key = sprintf('%s.%s', AlibabaCloud::getFacadeAccessor(), $name);

        return $this->getConfig()->get($key, $default);
    }

    /**
     * @return array
     */
    protected function getClientDefaultConfig(): array
    {
        return $this->getConfig()->get('defaults', []);
    }

    /**
     * Get a client by name.
     *
     * @param  string|null|integer  $name
     * @return Client
     */
    public function client($name = null): Client
    {
        return $this->driver($name);
    }

    /**
     * @inheritdoc
     */
    protected function createDriver($driver)
    {
        return $this->makeClient($this->configuration($driver));
    }

    /**
     * Make the AlibabaCloud client instance.
     *
     * @param  array  $config
     * @return Client
     */
    public function makeClient(array $config): Client
    {
        return new Client($config);
    }

    public function getDefaultDriver(): string
    {
        return $this->getDefaultClient();
    }

    /**
     * Get the default client name.
     *
     * @return string
     */
    public function getDefaultClient(): string
    {
        return Arr::get($this->config, 'default', 'default');
    }

    /**
     * Get the configuration for a client.
     *
     * @param  string  $name
     * @return array
     *
     * @throws InvalidArgumentException
     */
    protected function configuration(string $name): array
    {
        $name = $name ?: $this->getDefaultDriver();
        $config = $this->getPackageConfig("clients.$name");

        if (null === $config) {
            throw new InvalidArgumentException("AlibabaCloud client [{$name}] not configured.");
        }

        return array_merge($this->getClientDefaultConfig(), $config);
    }
}
