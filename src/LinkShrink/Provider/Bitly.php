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
use LinkShrink\Exception\InvalidArgument;
use LinkShrink\Exception\QuotaExceeded;
use LinkShrink\Exception\UnknownError;

class Bitly extends AbstractProvider implements Provider
{

    const SHORTEN_ENDPOINT_URL = 'https://api-ssl.bitly.com/v3/shorten';

    const EXPAND_ENDPOINT_URL = 'https://api-ssl.bitly.com/v3/expand';

    const STATUS_OK = 200;

    const STATUS_RATE_LIMIT_EXCEEDED = 403;

    const STATUS_INVALID_URI = 500;

    const STATUS_UNKNOWN_ERROR = 503;


    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @param HttpAdapterInterface $adapter
     * @param string $accessToken
     */
    function __construct(HttpAdapterInterface $adapter, $accessToken)
    {
        parent::__construct($adapter);

        $this->accessToken = $accessToken;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'bitly';
    }

    /**
     * @inheritdoc
     */
    public function getShortenedUrl($url)
    {
        $query = $this->buildShortenUrlQuery($url, $this->accessToken);
        $response = $this->executeQuery($query);

        return $response->url;
    }

    /**
     * @inheritdoc
     */
    public function getExpandedUrl($url)
    {
        $query = $this->buildExpandedUrlQuery($url, $this->accessToken);
        $response = $this->executeQuery($query);

        if (empty($response->expand[0]->long_url)) {
            throw new EmptyResponse(sprintf('Could not execute query %s', $query));
        }

        return $response->expand[0]->long_url;
    }

    /**
     * @param string $url
     * @param string $accessToken
     *
     * @return string
     */
    protected function buildShortenUrlQuery($url, $accessToken)
    {
        $parameters = array(
            'access_token' => $accessToken,
            'longUrl' => $url,
        );

        return static::SHORTEN_ENDPOINT_URL . '?' . http_build_query($parameters);
    }

    /**
     * @param string $url
     * @param string $accessToken
     *
     * @return string
     */
    protected function buildExpandedUrlQuery($url, $accessToken)
    {
        $parameters = array(
            'access_token' => $accessToken,
            'shortUrl' => $url,
        );

        return static::EXPAND_ENDPOINT_URL . '?' . http_build_query($parameters);
    }

    /**
     * @param string $query
     *
     * @return mixed
     */
    protected function executeQuery($query)
    {
        $content = (string) $this->getAdapter()->get($query)->getBody();
        if (empty($content)) {
            throw new EmptyResponse(sprintf('Could not execute query %s', $query));
        }

        $json = json_decode($content);
        if (!isset($json)) {
            throw new EmptyResponse(sprintf('Could not execute query %s', $query));
        }

        switch ($json->status_code) {
            case static::STATUS_OK:
                return $json->data;

            case static::STATUS_RATE_LIMIT_EXCEEDED:
                throw new QuotaExceeded(sprintf('Quota exceeded (%s): %s', $json->status_txt, $query));
                break;

            case static::STATUS_INVALID_URI:
                throw new InvalidArgument(sprintf('Invalid URI (%s): %s', $json->status_txt, $query));
                break;

            case static::STATUS_UNKNOWN_ERROR:
            default:
                throw new UnknownError(sprintf('Unknown error (%s): %s', $json->status_txt, $query));
                break;
        }
    }
}
