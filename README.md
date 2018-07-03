TinyListScraper is an easy to use Page Scraper

Requirements
============

**TinyListScraper 1.0** requires PHP `^5.6 || ~7.0.0 || ~7.1.0 || ~7.2.0`. PHP `DOMDocument` and `DOMXPath` extensions have to be loaded.


Usage
=====

The simplest usage (since version 7.0) of the library would be as follows:

```php
<?php
require_once __DIR__ . '/src/TinyListScraper.php';

// Load all results from all the scrapers
$oScraper = new TinyListScraper\TinyListScraper();
$aResults = $oScraper->get('search some text');
print_r($aResults);

// Load just the results from verge
$aResults = $oScraper->get('search some text', array('verge'));
print_r($aResults);
```

Creating a new Scraper
=====

All the scraper setting files are located in `src/scrapers/`. To create a new one copy an existing scraper or create a new file. The format has to be `[name].inc`.

A scraper setting file looks as followed:

```php
<?php
$aSettings = array(
    'url' => 'https://techcrunch.com/search/{$SEARCH}',
    'source' => 'techcrunch.com',
    'list' => array(
        'container' => array(
            'selector' => '//div[contains(@class,"post-block post-block")]'
        ),
        'link' => array(
            'selector' => './/a[contains(@class,"post-block__title__link")]',
            'attribute' => 'href'
        )
    ),
    'content' => array(
        'title' => array(
            'selector' => './/meta[@name="sailthru.title"]',
            'attribute' => 'content'
        ),
        'author' => array(
            'selector' => './/meta[@name="sailthru.author"]',
            'attribute' => 'content'
        ),
        'date' => array(
            'selector' => './/meta[@name="sailthru.date"]',
            'attribute' => 'content'
        ),
        'description' => array(
            'selector' => './/meta[@property="og:description"]',
            'attribute' => 'content'
        ),
        'tags' => array(
            'selector' => './/meta[@name="sailthru.tags"]',
            'attribute' => 'content'
        ),
        'image' => array(
            'selector' => './/meta[@name="sailthru.image.full"]',
            'attribute' => 'content'
        ),
        'thumb' => array(
            'selector' => './/meta[@name="sailthru.image.thumb"]',
            'attribute' => 'content'
        ),
        'text' => array(
            'selector' => './/div[contains(@class,"article-content")]/p'
        )
    )
);
```

Some important notes:
{$SEARCH} should be in the place of the URL where the search string occures.
The `DOMXPath` format is being used for finding elements. For more information check http://php.net/manual/de/class.domxpath.php
