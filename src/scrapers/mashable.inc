<?php
$aSettings = array(
    'url' => 'https://mashable.com/category/{$SEARCH}/',
    'source' => 'mashable.com',
    'list' => array(
        'container' => array(
            'selector' => '//div[contains(@class,"article-content")]'
        ),
        'link' => array(
            'selector' => './/h2[contains(@class,"article-title")]/a',
            'attribute' => 'href'
        )
    ),
    'content' => array(
        'title' => array(
            'selector' => './/meta[@property="og:title"]',
            'attribute' => 'content'
        ),
        'author' => array(
            'selector' => './/meta[@name="sailthru.author"]',
            'attribute' => 'content'
        ),
        'date' => array(
            'selector' => './/meta[@name="date"]',
            'attribute' => 'content'
        ),
        'description' => array(
            'selector' => './/meta[@name="description"]',
            'attribute' => 'content'
        ),
        'tags' => array(
            'selector' => './/meta[@name="zd_ptax"]',
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
            'selector' => './/section[contains(@class,"article-content")]/p'
        )
    )
);
