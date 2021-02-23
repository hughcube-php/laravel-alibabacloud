<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/2/23
 * Time: 11:21
 */

namespace HughCube\Laravel\AlibabaCloud;


use Illuminate\Support\Arr;

class Manager
{
    /**
     * The AlibabaCloud server configurations.
     *
     * @var array
     */
    protected $config;

    /**
     * The clients.
     *
     * @var Client[]
     */
    protected $clients = [];

    /**
     * Manager constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get a client by name.
     *
     * @param string|null $name
     *
     * @return Client
     */
    public function client($name = null)
    {
        $name = null == $name ? 'default' : $name;

        if (isset($this->clients[$name])) {
            return $this->clients[$name];
        }

        return $this->clients[$name] = $this->resolve($name);
    }

    /**
     * Resolve the given client by name.
     *
     * @param string|null $name
     *
     * @return Client
     *
     */
    protected function resolve($name = null)
    {
        return $this->makeClient($this->configuration($name));
    }

    /**
     * Make the AlibabaCloud client instance.
     *
     * @param string $name
     * @return Client
     */
    public function makeClient(array $config)
    {
        return new Client($config);
    }

    /**
     * Make the AlibabaCloud client instance from env
     *
     * @param string|null $idName
     * @param string|null $secretName
     * @param string|null $regionName
     * @return Client
     */
    public function makeMakeClientFromEnv($idName = null, $secretName = null, $regionName = null)
    {
        $idName = empty($idName) ? AlibabaCloud::KEY_ID_ENV_NAME : $idName;
        $secretName = empty($secretName) ? AlibabaCloud::KEY_SECRET_ENV_NAME : $secretName;
        $regionName = empty($regionName) ? AlibabaCloud::REGION_ENV_NAME : $regionName;

        return $this->makeClient(
            [
                "accessKey" => env($idName),
                "accessKeySecret" => env($secretName),
                "regionId" => env($regionName),
            ]
        );
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection()
    {
        return Arr::get($this->config, 'alibabaCloud.default', 'default');
    }

    /**
     * Get the configuration for a connection.
     *
     * @param string $name
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function configuration($name)
    {
        $name = $name ?: $this->getDefaultConnection();
        $clients = Arr::get($this->config, 'alibabaCloud.clients');

        if (is_null($config = Arr::get($clients, $name))) {
            throw new \InvalidArgumentException("AlibabaCloud client [{$name}] not configured.");
        }

        return $config;
    }
}
