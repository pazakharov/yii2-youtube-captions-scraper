<?php

namespace tests\unit;

use Yii;
use yii\httpclient\Client;
use yii\httpclient\Request;
use yii\httpclient\Response;
use Zakharov\YoutubeScreaper;
use Codeception\Util\HttpCode;

class YoutubeScreaperTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testClientInstance()
    {
        $screaper = Yii::createObject([
            'class' => YoutubeScreaper::class
        ]);
        expect($screaper->client)->isInstanceOf(Client::class);
    }

    public function testGetCaptionsBaseUrl()
    {
        $html = file_get_contents(codecept_data_dir('page.html'));
        $screaper = Yii::createObject([
            'class' => YoutubeScreaper::class,
            'client' => $this->getMokClient($html),
        ]);
        $url = $screaper->getCaptionsBaseUrl('https://www.youtube.com/watch?v=wNzql5TZ-iY');
        expect($url)->string();
    }

    public function testGetSubtitles()
    {
        $subtitlesXml = file_get_contents(codecept_data_dir('subtitles.xml'));
        $screaper = Yii::createObject([
            'class' => YoutubeScreaper::class,
            'client' => $this->getMokClient($subtitlesXml),
        ]);
        $captionUrl = 'https://www.youtube.com/api/timedtext?v=wNzql5TZ-iY&ei=ZBedZdulCcSkvdIPmfWl-A4&caps=asr&opi=112496729&xoaf=5&hl=en&ip=0.0.0.0&ipbits=0&expire=1704819156&sparams=ip,ipbits,expire,v,ei,caps,opi,xoaf&signature=862DDD91D1467210DB6A6A1599D5C5F560CCC69F.26A397CB13E3E86DF060C5AA58147416217A8EA5&key=yt8&kind=asr&lang=en';
        $subtitles = $screaper->getSubtitles($captionUrl);
        expect($subtitles)->array();
    }

    /**
     * @param mixed $data
     *
     * @return Client
     */
    private function getMokClient($data, $method = 'get')
    {
        $response = $this->make(
            Response::class,
            [
                'getContent' => function () use ($data) {
                    return $data;
                },
                'getStatusCode' => function () {
                    return HttpCode::OK;
                },
                'getIsOk' => function () {
                    return true;
                },
            ]
        );

        $request = $this->make(Request::class, [
            'send' => function () use ($response) {
                return $response;
            }
        ]);

        $client = $this->make(Client::class, [
            $method => $request
        ]);

        return $client;
    }
}
