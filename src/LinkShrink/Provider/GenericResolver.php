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
     * @param string $url
     *
     * @return void
     * @throws MethodNotImplemented
     */
    public function getShortenedUrl($url)
    {
        throw new MethodNotImplemented(sprintf('%s::%s not implimented.', get_class($this), 'shorten'));
    }

    /**
     * @inheritdoc
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
