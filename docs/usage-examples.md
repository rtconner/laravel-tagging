Usage Examples
============

```php
$article = Article::with('tagged')->first(); // eager load

foreach($article->tagged as $tagged) {
	echo $tagged->tag_name . ' with url slug of ' . $tagged->tag_slug;
}

foreach($article->tags as $tag) {
	echo $tag->name . ' with url slug of ' . $tag->slug;
}

$article->tag('Gardening'); // attach the tag
$article->tag('Gardening, Floral'); // attach the tag
$article->tag(['Gardening', 'Floral']); // attach the tag

$article->untag('Cooking'); // remove Cooking tag
$article->untag(); // remove all tags

$article->retag(['Fruit', 'Fish']); // delete current tags and save new tags
$article->retag('Fruit', 'Fish');
$article->retag('Fruit, Fish');

$tagged = $article->tagged; // return Collection of rows tagged to article
$tags = $article->tags; // return Collection the actual tags (is slower than using tagged)

$article->tagNames(); // get array of related tag names	

Article::withAnyTag('Gardening, Cooking')->get(); // fetch articles with any tag listed
Article::withAnyTag(['Gardening','Cooking'])->get(); // different syntax, same result as above
Article::withAnyTag('Gardening','Cooking')->get(); // different syntax, same result as above

Article::withAllTags('Gardening, Cooking')->get(); // only fetch articles with all the tags
Article::withAllTags(['Gardening', 'Cooking'])->get();
Article::withAllTags('Gardening', 'Cooking')->get();

Conner\Tagging\Model\Tag::where('count', '>', 2)->get(); // return all tags used more than twice

Article::existingTags(); // return collection of all existing tags on any articles
```
