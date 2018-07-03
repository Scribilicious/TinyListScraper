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
