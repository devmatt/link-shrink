<?php

namespace LinkShrink\Provider;

use Ivory\HttpAdapter\HttpAdapterException;
use LinkShrink\Exception\MethodNotImplemented;

class GenericResolver extends AbstractProvider implements Provider
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'generic_resolver';
    }

    /**
     * @param string$url
     *
     * @return void
     * @throws MethodNotImplemented
     */
    public function getShortenedUrl($url)
    {
        throw new MethodNotImplemented(sprintf('%s::%s not implimented.', get_class($this), 'shorten'));
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function getExpandedUrl($url)
    {
        /**
         * Attempt to use a HEAD request to fetch the redirect location, sometimes fails due to
         * incorrect Content-Length header in the response
         */
        try {
            return $this->getAdapter()->head($url)->getHeader('location');
        } catch (HttpAdapterException $e) {
            return $this->getAdapter()->get($url)->getHeader('location');
        }
    }
}
