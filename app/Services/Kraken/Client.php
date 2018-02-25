<?php

namespace App\Services\Kraken;

use App\Contracts\Services\Kraken\Client as ClientContract;
use GuzzleHttp\Client as HttpClient;

class Client implements ClientContract
{
    const API_URL = 'https://api.kraken.com';
    const API_VERSION = 0;

    /**
     * API key
     *
     * @var string
     */
    protected $key;

    /**
     * API secret
     *
     * @var string
     */
    protected $secret;

    /**
     * @var HttpClient
     */
    protected $client;

    /**
     * Two-factor password
     *
     * @var string
     */
    private $otp;

    /**
     * @param HttpClient $client
     * @param string $key API key
     * @param string $secret API secret
     * @param string|null $otp Two-factor password (if two-factor enabled, otherwise not required)
     */
    public function __construct(HttpClient $client, string $key, string $secret, string $otp = null)
    {
        $this->client = $client;
        $this->key = $key;
        $this->secret = $secret;
        $this->otp = $otp;
    }

    /**
     * @param string $method
     * @param array $parameters
     * @param bool $isPublic
     * @return array
     * @throws KrakenApiErrorException
     */
    public function request(string $method, array $parameters = [], bool $isPublic = true): array
    {
        $headers = [
            'User-Agent' => 'Kraken PHP API Agent',
        ];

        if (!$isPublic) {
            if ($this->otp) {
                $parameters['otp'] = $this->otp;
            }

            $parameters['nonce'] = $this->generateNonce();

            $headers['API-Key'] = $this->key;
            $headers['API-Sign'] = $this->generateSign($method, $parameters);
        }

        $response = $this->client->post($this->buildUrl($method, $isPublic), [
            'headers' => $headers,
            'form_params' => $parameters,
            'verify' => true
        ]);

        $result = \GuzzleHttp\json_decode(
            $response->getBody()->getContents(),
            true
        );

        if (!empty($result['error'])) {
            throw new KrakenApiErrorException(implode(', ', $result['error']));
        }

        return $result['result'];
    }

    /**
     * @param string $method
     * @param bool $isPublic
     * @return string
     */
    protected function buildUrl(string $method, bool $isPublic = true): string
    {
        return static::API_URL . $this->buildPath($method, $isPublic);
    }

    /**
     * @param string $method
     * @param bool $isPublic
     * @return string
     */
    protected function buildPath(string $method, bool $isPublic = true): string
    {
        $queryType = $isPublic ? 'public' : 'private';

        return '/' . static::API_VERSION . '/' . $queryType . '/' . $method;
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return string
     */
    protected function generateSign(string $method, array $parameters = []): string
    {
        // build the POST data string
        $queryString = http_build_query($parameters, '', '&');

        return base64_encode(
            hash_hmac(
                'sha512',
                $this->buildPath($method, false) . hash('sha256', $parameters['nonce'] . $queryString, true),
                base64_decode($this->secret),
                true
            )
        );
    }

    /**
     * Generate a 64 bit nonce using a timestamp at microsecond resolution
     * string functions are used to avoid problems on 32 bit systems
     *
     * @return string
     */
    protected function generateNonce(): string
    {
        $nonce = explode(' ', microtime());
        return $nonce[1] . str_pad(substr($nonce[0], 2, 6), 6, '0');
    }
}