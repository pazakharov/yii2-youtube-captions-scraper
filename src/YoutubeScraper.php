<?php

namespace Zakharov;

use Exception;
use yii\di\Instance;
use yii\base\Component;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;

class YoutubeScraper extends Component
{
    /**
     * filter captions by language
     * @var string
     */
    public $languageCode = 'en';
    /**
     * @var array|Client
     */
    public $client = [];
    /**
     * @return void
     */
    public function init()
    {
        parent::init();
        if (empty($this->client)) {
            $this->client = $this->getDefaultClientConfig();
        }
        $this->client = Instance::ensure($this->client, Client::class);
    }

    /**
     * @return array
     */
    public function getDefaultClientConfig()
    {
        return [
            'class' => Client::class,
            'transport' => CurlTransport::class,
            'requestConfig' => [
                'headers' => [
                    'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                    'accept-language' => 'en-En,en;q=0.9',
                    'cache-control' => 'no-cache',
                    'pragma' => 'no-cache',
                    'sec-ch-ua' => '"Google Chrome";v="117", "Not;A=Brand";v="8", "Chromium";v="117"',
                    'sec-ch-ua-mobile' => '?0',
                    'sec-ch-ua-platform' => '"Windows"',
                    'sec-fetch-dest:' => 'document',
                    'sec-fetch-mode' => 'navigate',
                    'sec-fetch-site' => 'none',
                    'sec-fetch-user' => '?1',
                    'upgrade-insecure-requests' => '1',
                    'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36'
                ],
                'options' => [
                    'proxy' => env('HTTPCLIENT_PROXY'),
                    'timeout' => 20,
                    'followLocation' => true,
                    'maxRedirects' => 10,
                    'sslVerifyPeer' => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                ]
            ]
        ];
    }

    /**
     * @param string $videoUrl
     *
     * @return string
     */
    public function getCaptionsBaseUrl(string $videoUrl)
    {
        $request = $this->client->get($videoUrl);
        $response = $request->send();
        if (!$response->isOk) {
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getContent();
            throw new Exception("Failed to get captions: $statusCode: $responseBody");
        }
        $html = $response->getContent();
        preg_match('/"captionTracks":([^\]]*])/', $html, $matches);
        if (empty($matches[1]) || !strpos($matches[1], '"baseUrl":')) {
            throw new Exception("Url not find: $matches[1]");
        }
        $results = json_decode($matches[1], true);
        $result = array_filter($results, function ($result) {
            return $result['languageCode'] == $this->languageCode;
        });
        if (!count($result)) {
            throw new Exception("Url not find by languageCode: $matches[1]");
        }
        $baseUrl = $result[0]['baseUrl'] ?? null;
        return $baseUrl;
    }

    /**
     * @param string $captionUrl
     *
     * @return array
     */
    public function getSubtitles(string $captionUrl)
    {
        $request = $this->client->get($captionUrl);
        $response = $request->send();
        if (!$response->isOk) {
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getContent();
            throw new Exception("Failed to get subs: $statusCode: $responseBody");
        }
        $subtitlesContent = $response->getContent();
        $subtitles = $this->parseXmlTextNodes($subtitlesContent);
        return $subtitles;
    }

    /**
     * Parses an XML string and returns the content of all <text> nodes as an array.
     *
     * @param string $xml The XML string to be parsed.
     * @return array The content of all <text> nodes as an array.
     */
    private function parseXmlTextNodes($xml)
    {
        $result = [];
        $xmlObject = simplexml_load_string($xml);
        if ($xmlObject) {
            $textNodes = $xmlObject->xpath('//text');
            foreach ($textNodes as $textNode) {
                $result[] = (string) $textNode;
            }
        }
        return $result;
    }
}
