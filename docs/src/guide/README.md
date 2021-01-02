# Introduction

When implementing the [JSON:API](https://jsonapi.org) one of the first challenges is to map application data into JSON:API [resource object](https://jsonapi.org/format/#document-resource-objects).
Basic mapping and serialization implementation is simple, but becomes tricky when we want to reuse the mapping or implement such features as [spare fieldsets](https://jsonapi.org/format/#fetching-sparse-fieldsets) or [compound documents](https://jsonapi.org/format/#document-compound-documents).

JSON:API Resource library build to map PHP class properties and methods into resource object attributes, relationships, links.
It is suitable for ORM's like [Doctrine](https://www.doctrine-project.org/) or [Eqoulent](https://laravel.com/docs/5.0/eloquent) because their data already converted into objects and classes.

## How It Works
Library is build on top of [PHP Attributes](https://www.php.net/manual/en/language.attributes.overview.php) feature, that was implemented in PHP 8.0 so unfortunately can be used only with PHP 8.0.

By appending PHP attributes to class, property or method we create mapping. We read this mapping when needed and convert it to metadata that can useful for serialization\hydration tasks.

Mapping becomes simple and self explained:
```php{3,6,9,15}
use JSONAPI\Resource\Attributes as JSONAPI;

#[JSONAPI\Resource('articles')]
class Article
{
    #[JSONAPI\Attribute()]
    public string $title = 'JSON:API paints my bikeshed!';

    #[JSONAPI\Relationship('author')]
    public function getAuthor(): ?People
    {
        return $this->author; // Or fetch comments from data provider
    }

    #[JSONAPI\Relationship()]
    public function comments(): array
    {
        return $this->comments; // Or fetch comments from data provider
    }
}
```

## Alternatives

### Fractal
If you need to serialize PHP arrays, and you don't use ORM into JSON:API resource objects, we suggest to use [Fractal](https://fractal.thephpleague.com/).

Fractal can be used for PHP objects serialisation as well, but it is transformers can't be reused for later resource hydration (creating or updating).
