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
        $name = empty($name) ? $this->getDefaultClient() : $name;
        if (!isset($this->clients[$name])) {
            $this->clients[$name] = $this->resolve($name);
        }

        return $this->clients[$name];
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
     * @param array $config
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
     * @param string|null $accountName
     * @return Client
     */
    public function makeClientFromEnv(
        string $idName = null,
        string $secretName = null,
        string $regionName = null,
        string $accountName = null
    ) {
        $idName = empty($idName) ? AlibabaCloud::ACCESS_KEY_ID_ENV_NAME : $idName;
        $secretName = empty($secretName) ? AlibabaCloud::ACCESS_KEY_SECRET_ENV_NAME : $secretName;
        $regionName = empty($regionName) ? AlibabaCloud::REGION_ID_ENV_NAME : $regionName;
        $accountName = empty($accountName) ? AlibabaCloud::ACCOUNT_ID_ENV_NAME : $accountName;

        return $this->makeClient([
            "AccessKeyID" => env($idName),
            "AccessKeySecret" => env($secretName),
            "RegionId" => env($regionName),
            "AccountId" => env($accountName),
        ]);
    }

    /**
     * Get the default client name.
     *
     * @return string
     */
    public function getDefaultClient()
    {
        return Arr::get($this->config, 'default', 'default');
    }

    /**
     * Get the configuration for a client.
     *
     * @param string $name
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function configuration($name)
    {
        $name = $name ?: $this->getDefaultClient();
        $clients = Arr::get($this->config, 'clients');

        if (is_null($config = Arr::get($clients, $name))) {
            throw new \InvalidArgumentException("AlibabaCloud client [{$name}] not configured.");
        }

        return $config;
    }
}
