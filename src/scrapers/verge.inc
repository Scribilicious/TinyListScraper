<?php
$aSettings = array(
    'url' => 'https://www.theverge.com/search?order=date&q={$SEARCH}&type=Article',
    'source' => 'verge.com',
    'list' => array(
        'container' => array(
            'selector' => '//div[contains(@class,"c-compact-river__entry")]'
        ),
        'link' => array(
            'selector' => './/a',
            'attribute' => 'href'
        ),
        'thumb' => array(
            'selector' => './/noscript/img',
            'attribute' => 'src'
        )
    ),
    'content' => array(
        'title' => array(
            'selector' => './/meta[@property="og:title"]',
            'attribute' => 'content'
        ),
        'author' => array(
            'selector' => './/meta[@property="author"]',
            'attribute' => 'content'
        ),
        'date' => array(
            'selector' => './/meta[@property="article:published_time"]',
            'attribute' => 'content'
        ),
        'description' => array(
            'selector' =>'.//meta[@name="description"]',
            'attribute' => 'content'
        ),
        'tags' => array(
            'selector' => './/meta[@name="sailthru.tags"]',
            'attribute' => 'content'
        ),
        'image' => array(
            'selector' => './/meta[@property="og:image"]',
            'attribute' => 'content'
        ),
        'text' => array(
            'selector' => './/div[contains(@class,"c-entry-content")]/p'
        )
    )
);
