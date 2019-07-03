Tag Groups
============

TagGroups are a simple feature that let you group a set of tags together

```php
php artisan tagging:create-group MyTagGroup
```

Set the tag group for a tag

```php
$tag->setGroup('MyTagGroup');
```

To get all the tags in a certain group

```php
Tag::inGroup('MyTagGroup')->get()
```

Check if a tag is in a group

```php
$tag->isInGroup('MyTagGroup');
```