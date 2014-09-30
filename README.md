Laravel Tag Plugin
============

[![Latest Stable Version](https://poser.pugx.org/rtconner/laravel-tagging/v/stable.svg)](https://packagist.org/packages/rtconner/laravel-tagging)
[![Total Downloads](https://poser.pugx.org/rtconner/laravel-tagging/downloads.svg)](https://packagist.org/packages/rtconner/laravel-tagging)
[![License](https://poser.pugx.org/rtconner/laravel-tagging/license.svg)](https://packagist.org/packages/rtconner/laravel-tagging)
[![Build Status](https://travis-ci.org/rtconner/laravel-tagging.svg?branch=master)](https://travis-ci.org/rtconner/laravel-tagging)

This package is not meant to handle javascript or html in any way. This package handles database storage and read/writes only.

There are no real limits on what characters can be used in a tag. It uses a slug transform to determine if two tags are identical ("sugar-free" and "Sugar Free" would be treated as the same tag). Tag display names are run through Str::title()

#### Composer Install

    "require": {
        "rtconner/laravel-tagging": "0.4.*"
    }

#### Run the migrations

	php artisan migrate --package=rtconner/laravel-tagging
	
#### Setup your models

    class Article extends \Eloquent {
        use Conner\Tagging\TaggableTrait;
    }

#### Sample Usage

    $article->tag('Gardening'); // attach the tag
    
    $article->untag('Cooking'); // remove Cooking tag
    $article->untag(); // remove all tags
    
    $article->retag(array('Fruit', 'Fish')); // delete current tags and save new tags
    
    $article->tagged(); // return Collection of rows tagged to article
    
    $article->tagNames(); // get array of related tag names	
    
    Article::withAnyTag('Gardening, Cooking')->get() // fetch articles with any tag listed
    Article::withAnyTag(array('Gardening','Cooking'))->get() // different sytax same result as above
    
    Article::withAllTags('Gardening, Cooking')->get() // only fetch articles with all the tags
    
    Conner\Tagging\Tag::where('count', '>', 2)->get(); // return all tags used more than twice

#### (Optional) Configuration

If you want to use any of the configuration options, you must enable the TaggingServiceProvider and also, publish the config.php

    php artisan config:publish rtconner/laravel-tagging
   
    'providers' => array(
        'Conner\Tagging\TaggingServiceProvider',
    );
    
After these two steps are done, edit at the config.php with your prefered settings.

#### Credits

 - Robert Conner - http://smartersoftware.net