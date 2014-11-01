<?php

namespace LinkShrink\Provider;

use Ivory\HttpAdapter\HttpAdapterInterface;
use LinkShrink\Exception\EmptyResponse;
use LinkShrink\Exception\UnknownError;

class Google extends AbstractProvider implements Provider
{

    const SHORTEN_ENDPOINT_URL = 'https://www.googleapis.com/urlshortener/v1/url';

    const EXPAND_ENDPOINT_URL = 'https://www.googleapis.com/urlshortener/v1/url';


    const STATUS_OK = 200;

    const STATUS_RATE_LIMIT_EXCEEDED = 403;

    const STATUS_INVALID_URI = 500;

    const STATUS_UNKNOWN_ERROR = 503;

    /**
     * @var string|null
     */
    protected $apiKey;

    /**
     * @param HttpAdapterInterface $adapter
     * @param string|null $apiKey
     */
    public function __construct(HttpAdapterInterface $adapter, $apiKey = null)
    {
        parent::__construct($adapter);

        $this->apiKey = $apiKey;
    }

    public function getName()
    {
        return 'google';
    }

    /**
     * @param $url
     *
     * @return mixed
     */
    public function getShortenedUrl($url)
    {
        $apiUrl = $this->buildShortenUrl();
        $apiParameters = $this->buildShortenUrlParameters($url);

        return $this->executeShortenUrlQuery($apiUrl, $apiParameters);
    }

    /**
     * @param $url
     *
     * @return mixed
     */
    public function getExpandedUrl($url)
    {
        $apiUrl = $this->buildExpandedUrl($url);

        return $this->executeExpandUrlQuery($apiUrl);
    }

    protected function buildShortenUrl()
    {
        $url = static::SHORTEN_ENDPOINT_URL;
        if ($this->apiKey) {
            $url .= '?' . http_build_query(array('key' => $this->apiKey));
        }

        return $url;
    }

    /**
     * @param $url
     *
     * @return string
     */
    protected function buildShortenUrlParameters($url)
    {
        $parameters = array(
            'longUrl' => $url,
        );

        return json_encode($parameters);
    }

    protected function executeShortenUrlQuery($query, $parameters)
    {
        $content = (string) $this->getAdapter()->post($query, array('Content-Type: application/json'),
            $parameters)->getBody();
        if (empty($content)) {
            throw new EmptyResponse(sprintf('Could not execute query %s', $query));
        }

        $json = json_decode($content);
        if (!isset($json)) {
            throw new EmptyResponse(sprintf('Could not execute query %s', $query));
        }

        if (!empty($json->id)) {
            return $json->id;
        }

        throw new UnknownError(sprintf('Unknown error (%s): %s', $content, $query));
    }

    /**
     * @param $url
     *
     * @return string
     */
    protected function buildExpandedUrl($url)
    {
        $parameters = array(
            'shortUrl' => $url,
        );
        if ($this->apiKey) {
            $parameters['key'] = $this->apiKey;
        }

        return static::EXPAND_ENDPOINT_URL . '?' . http_build_query($parameters);
    }

    protected function executeExpandUrlQuery($query)
    {
        $content = (string) $this->getAdapter()->get($query)->getBody();
        if (empty($content)) {
            throw new EmptyResponse(sprintf('Could not execute query %s', $query));
        }

        $json = json_decode($content);
        if (!isset($json)) {
            throw new EmptyResponse(sprintf('Could not execute query %s', $query));
        }

        if (!empty($json->longUrl)) {
            return $json->longUrl;
        }

        throw new UnknownError(sprintf('Unknown error (%s): %s', $content, $query));
    }
}
