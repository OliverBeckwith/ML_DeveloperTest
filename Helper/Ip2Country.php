<?php

namespace ML\DeveloperTest\Helper;

use \Magento\Framework\HTTP\Client\Curl as CurlClient;
use \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

/**
 * Helper class for looking up a country based on a given IP
 */
class Ip2Country
{
    private CurlClient $curlClient;
    private RemoteAddress $remoteAddress;

    protected const IP2COUNTRY_BASE_URL = "https://ip2c.org/?ip=";

    public function __construct(
        CurlClient $curlClient,
        RemoteAddress $remoteAddress,
    ) {
        $this->curlClient = $curlClient;
        $this->remoteAddress = $remoteAddress;
    }

    protected function constructRequestUrl(string $ip): string
    {
        return $this::IP2COUNTRY_BASE_URL . "$ip";
    }

    protected function parseResponse(string $body): ?string
    {
        // Output structure is consistent from ip2c: https://about.ip2c.org/#outputs
        [$success, $two_letter_code] = explode(";", $body);
        if ($success !== "1") {
            return null;
        }
        return $two_letter_code;
    }

    /**
     * Get the 2 character ISO-3166 country code for the current user
     */
    public function getCurrentCountryCode(): ?string
    {
        $ip = $this->remoteAddress->getRemoteAddress();
        return $this->getCountryCode($ip);
    }

    /**
     * Get the 2 character ISO-3166 country code for the specified IP
     */
    public function getCountryCode(string $ip): ?string
    {
        try {
            $this->curlClient->get($this->constructRequestUrl($ip));
            $body = $this->curlClient->getBody();
            return $this->parseResponse($body);
        } catch (\Exception $e) {
            return null;
        }
    }
}
