# Youtube Scraper
The Youtube Scraper is a PHP package that allows you to scrape captions and subtitles from Youtube videos. It uses the Yii 2 framework and the Guzzle HTTP client to make requests to the Youtube API.

### Installation
You can install the Youtube Scraper using Composer:
```bash
    composer require pazakharov/yii2-youtube-captions-scraper
```
### Usage
To use the Youtube Scraper, you first need to create an instance of the YoutubeScraper class and set the languageCode property to the language code of the captions you want to scrape. You can then use the getCaptionsBaseUrl and getSubtitles methods to retrieve the base URL and subtitles, respectively.

Here's an example usage:
```php
use Zakharov\YoutubeScraper;

        $scraper = Yii::createObject([
            'class' => YoutubeScraper::class,
        ]);
        $captionUrl = $scraper->getCaptionsBaseUrl('https://www.youtube.com/watch?v=wNzql5TZ-i');
        $subtitles = $scraper->getSubtitles($captionUrl);
```
In this example, we're scraping the captions (subtitles) for the video with the ID wNzql5TZ-i. The getCaptionsBaseUrl method returns the base URL for the captions, which we then pass to the getSubtitles method to retrieve the subtitles.

Note that the getSubtitles method returns an array of strings, where each string represents a subtitle. You can then use this array to display the subtitles in your application.

For use proxy use .env var HTTPCLIENT_PROXY=http://login:password@domain:port or you can configure and provide own instance of Client to YoutubeScraper class.

### Testing and Contributing
The Youtube Scraper includes a test suite to ensure that it works correctly. You can run the tests using the phpunit command:
```bash
vendor/bin/codecept run unit
```
This will run the test suite and output the results.

The Youtube Scraper follows the PSR-2 coding standards. You can check the code for compliance using the phpcs command:

```bash
composer check-code
```
Fix code by phpcbf
```bash
composer fix-code
```
License
The Youtube Scraper is licensed under the MIT license. You can use it free of charge and without any restrictions.