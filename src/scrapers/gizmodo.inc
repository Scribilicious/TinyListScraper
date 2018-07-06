<?php
$aSettings = array(
    'url' => 'https://gizmodo.com/search?q={$SEARCH}',
    'source' => 'gizmodo.com',
    'list' => array(
        'container' => array(
            'selector' => '//div[contains(@class,"post-wrapper js_post-wrapper")]'
        ),
        'link' => array(
            'selector' => './/a',
            'attribute' => 'href'
        ),
        'thumb' => array(
            'selector' => './/picture/source',
            'attribute' => 'data-srcset'
        )
    ),
    'content' => array(
        'title' => array(
            'selector' => './/meta[@property="og:title"]',
            'attribute' => 'content'
        ),
        'author' => array(
            'selector' => './/meta[@name="author"]',
            'attribute' => 'content'
        ),
        'date' => array(
            'selector' => './/time',
            'attribute' => 'datetime'
        ),
        'description' => array(
            'selector' =>'.//meta[@name="description"]',
            'attribute' => 'content'
        ),
        'tags' => array(
            'selector' => './/meta[@name="news_keywords"]',
            'attribute' => 'content'
        ),
        'image' => array(
            'selector' => './/meta[@property="og:image"]',
            'attribute' => 'content'
        ),
        'text' => array(
            'selector' => './/div[contains(@class,"post-content")]/p'
        )
    )
);
