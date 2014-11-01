<?php
/**
 * Link Shrink
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE file
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Matthew Nessworthy <matthew@devmatt.co.za>
 * @copyright Copyright (c) Matthew Nessworthy
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/devmatt/link-shrink
 */
namespace LinkShrink;

use LinkShrink\Exception\ProviderNotRegistered;
use LinkShrink\Provider\Provider;

class LinkShrink
{
    /**
     * @var Provider
     */
    protected $provider;

    /**
     * @var array [Provider]
     */
    protected $providers;

    /**
     * @param Provider $provider
     */
    function __construct(Provider $provider = null)
    {
        if ($provider instanceof Provider) {
            $this->setProvider($provider);
        }
    }


    /**
     * Returns a shortened version of the provided URL
     *
     * @param string $url
     *
     * @return string
     */
    public function shorten($url)
    {
        return $this->getProvider()->getShortenedUrl($url);
    }

    /**
     * Returns the original URL based on the given shortened URL
     *
     * @param string $url
     *
     * @return string
     */
    public function expand($url)
    {
        return $this->getProvider()->getExpandedUrl($url);
    }

    /**
     * @return Provider
     */
    public function getProvider()
    {
        if ($this->provider === null) {
            if (count($this->providers)) {
                $this->switchProvider(key($this->providers));
            } else {
                throw new \RuntimeException('No provider set for request.');
            }
        }

        return $this->provider;
    }

    /**
     * @param Provider $provider
     */
    public function setProvider(Provider $provider)
    {
        $this->registerProvider($provider);
        $this->switchProvider($provider->getName());
    }

    /**
     * @param Provider $provider
     */
    public function registerProvider(Provider $provider)
    {
        $this->providers[$provider->getName()] = $provider;
    }

    /**
     * @param array [Provider] $providers
     */
    public function registerProviders(array $providers = array())
    {
        foreach ($providers as $provider) {
            $this->registerProvider($provider);
        }
    }

    /**
     * @param string $name
     */
    public function switchProvider($name)
    {
        if (isset($this->providers[$name])) {
            $this->provider = $this->providers[$name];
        } else {
            throw new ProviderNotRegistered(sprintf('Provider %s not registered.', $name));
        }
    }
}
