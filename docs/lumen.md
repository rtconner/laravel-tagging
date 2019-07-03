Lumen Installation
============

Lumen does not have a vendor:publish command, so you will need to create or copy the provided migrations and config file into their respective directory.

In app\bootstrap\app.php

```php
// Add this line in your config section
$app->configure('tagging');
// Add this line in your service provider section
$app->register(Conner\Tagging\Providers\LumenTaggingServiceProvider::class);
```

After these two steps are done, you can edit config/tagging.php with your preferred settings.