Events
============

The `Taggable` trait will fire off two events.

```php
Conner\Tagging\Events\TagAdded;

Conner\Tagging\Events\TagRemoved;
```

You can add listeners and track these events.

```php
\Event::listen(TagAdded::class, function(TagAdded $event){
	\Log::debug($event->model->title . ' was tagged');
});
```

```php
\Event::listen(TagRemoved::class, function(TagRemoved $event){
	\Log::debug($event->tagSlug . ' was removed');
});
```