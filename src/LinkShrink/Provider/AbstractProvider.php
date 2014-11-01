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

use Ivory\HttpAdapter\HttpAdapterInterface;

abstract class AbstractProvider
{
    /**
     * @var HttpAdapterInterface
     */
    protected $adapter;

    /**
     * @param HttpAdapterInterface $adapter
     */
    public function __construct(HttpAdapterInterface $adapter)
    {
        $this->setAdapter($adapter);
    }

    /**
     * @return HttpAdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param HttpAdapterInterface $adapter
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }
}

