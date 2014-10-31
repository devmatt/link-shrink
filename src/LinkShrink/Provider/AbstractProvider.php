<?php

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

