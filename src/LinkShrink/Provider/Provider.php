<?php

namespace LinkShrink\Provider;

interface Provider
{
    /**
     * @param $url
     *
     * @return string
     */
    public function getShortenedUrl($url);

    /**
     * @param $url
     *
     * @return string
     */
    public function getExpandedUrl($url);

    /**
     * @return string
     */
    public function getName();
}
