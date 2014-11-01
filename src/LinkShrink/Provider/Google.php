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

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'google';
    }

    /**
     * @inheritdoc
     */
    public function getShortenedUrl($url)
    {
        $apiUrl = $this->buildShortenUrl();
        $apiParameters = $this->buildShortenUrlParameters($url);

        return $this->executeShortenUrlQuery($apiUrl, $apiParameters);
    }

    /**
     * @inheritdoc
     */
    public function getExpandedUrl($url)
    {
        $apiUrl = $this->buildExpandedUrl($url);

        return $this->executeExpandUrlQuery($apiUrl);
    }

    /**
     * @return string
     */
    protected function buildShortenUrl()
    {
        $url = static::SHORTEN_ENDPOINT_URL;
        if ($this->apiKey) {
            $url .= '?' . http_build_query(array('key' => $this->apiKey));
        }

        return $url;
    }

    /**
     * @param string $url
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

    /**
     * @param string $query
     * @param array|string $parameters
     *
     * @return string
     */
    protected function executeShortenUrlQuery($query, $parameters = array())
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
     * @param string$url
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

    /**
     * @param string $query
     *
     * @return string
     */
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
