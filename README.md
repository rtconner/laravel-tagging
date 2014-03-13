Laravel Tag Plugin
============

This package is not meant to handle javascript or html in any way. This package handles database storage and read/writes only.

There are no real limits on what characters can be used in a tag. It uses a slug transform to determine if two tags are identical ("sugar-free" and "Sugar Free" would be treated as the same tag). Tag display names are run through Str::title()

#### Composer Install

    "require": {
        "rtconner/laravel-tagging": "dev-master"
    }

#### Run the migrations

	php artisan migrate --package=rtconner/laravel-tagging
	
#### Setup your models

    class Article extends \Eloquent {
        use Conner\Tagging\Taggable;
    }

#### Sample Usage

    $article->tag('Gardening'); // attach the tag
    
    $article->untag('Cooking'); // remove Cooking tag
    
    $article->tagged(); // return Collection of rows tagged to article
    
    $article->tagNames(); // get array of related tag names	
    
    Article::withTag('Gardening')->get(); // fetch all articles with tag
    
    Article::withTags('Gardening, Cooking')->get() // fetch all articles with given tags
    
    Article::withTags(['Gardening','Cooking'])->get() // fetch all articles with given tags
    
    Conner\Tagging\Tag::where('count', '>', 2)->get(); // return all tags used more than twice
