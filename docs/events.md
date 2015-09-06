Events
============

The `Taggable` trait will fire off two events.

```php
Conner\Tagging\Events\TagAdded;

Conner\Tagging\Events\TagRemoved;
```

You can add listeners and track these events.

```php
\Event::listen(Conner\Tagging\Events\TagAdded::class, function($article){
	\Log::debug($article->title . ' was tagged');
});
```