<?php

namespace App\Services\Kraken;

use App\Contracts\Services\Kraken\Client as ClientContract;
use App\Contracts\Services\Kraken\Order as OrderContract;
use App\Log;
use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;

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
     * Get account balance
     *
     * @return Collection
     * @throws KrakenApiErrorException
     */
    public function getAccountBalance(): Collection
    {
        $result = $this->request('Balance', [], false);

        return collect($result)->map(function ($amount, $currency) {
            return new Balance($currency, $amount);
        });
    }

    /**
     * Get trade balance
     *
     * @return array
     * @throws KrakenApiErrorException
     */
    public function getTradeBalance(): array
    {
        return $this->request('TradeBalance', [], false);
    }

    /**
     * Get open orders
     *
     * @param bool $trades Whether or not to include trades in output
     * @return array
     * @throws KrakenApiErrorException
     */
    public function getOpenOrders(bool $trades = false): array
    {
        return $this->request('OpenOrders', ['trades' => $trades], false);
    }

    /**
     * Get closed orders
     *
     * @param bool $trades Whether or not to include trades in output
     * @param Carbon|null $start Starting date
     * @param Carbon|null $end Ending date
     * @return array
     * @throws KrakenApiErrorException
     */
    public function getClosedOrders(bool $trades = false, Carbon $start = null, Carbon $end = null): array
    {
        $parameters = ['trades' => $trades];

        if ($start) {
            $parameters['start'] = $start->timestamp;
        }

        if ($end) {
            $parameters['end'] = $end->timestamp;
        }

        return $this->request('ClosedOrders', $parameters, false);
    }

    /**
     * Add standard order
     *
     * @param OrderContract $order
     * @return OrderStatus
     * @throws KrakenApiErrorException
     */
    public function addOrder(OrderContract $order): OrderStatus
    {
        $result = $this->request('AddOrder', $order->toArray(), false);

        return new OrderStatus($result['txid'][0], $result['descr']);
    }

    /**
     * Cancel open order
     *
     * @param string $transactionId
     * @return array
     * @throws KrakenApiErrorException
     */
    public function cancelOrder(string $transactionId): array
    {
        return $this->request('CancelOrder', ['txid' => $transactionId], false);
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

        Log::message(sprintf('Kraken API [%s]: %s', $method, json_encode($parameters)));

        $response = $this->client->post($this->buildUrl($method, $isPublic), [
            'headers' => $headers,
            'form_params' => $parameters,
            'verify' => true
        ]);

        $result = $this->decodeResult(
            $response->getBody()->getContents()
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
     * Message signature using HMAC-SHA512 of (URI path + SHA256(nonce + POST data)) and base64 decoded secret API key
     *
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

    /**
     * @param $response
     * @return array
     */
    protected function decodeResult($response): array
    {
        Log::message("Kraken API response: " . $response);

        return \GuzzleHttp\json_decode(
            $response,
            true
        );
    }
}