# PHP Attributes
Library use [PHP Attributes](https://www.php.net/manual/en/language.attributes.overview.php) feature for mapping class into resource object.
Lets look on the attributes in more details.

::: tip
To use PHP attributes and make our code clear import attributes namespace and give it alias.
```php
use JSONAPI\Resource\Attributes as JSONAPI;
```
:::

## Resource Object
### Class
To map class to be [resource object](https://jsonapi.org/format/#document-resource-objects) we must define the identifiers `type` and `id`.
Use `JSONAPI\Resource\Attributes\Resource` PHP attribute, to map class to resource object.

### Type
Type is static, and is same per each object instance of resource class.

#### Generated
By default, type will be generated from class name, by making lower case first letter.
```php{1}
#[JSONAPI\Resource()] \\ type === 'article'
class Article
```

#### User defined
We can pass `type` as first constructor parameter.

```php{1}
#[JSONAPI\Resource('articles')]
class Article
```

### Id
Each object implementing the resource class going to have different id.
Id can be fetched from object method or property.
`idFetcher` is second `string` constructor parameter, and it is `getId` by default.
It is identifies method or property that must be called to fetch resource id.

Example how to define resource with `id` identifier represented by `id` property.
```php{1}
#[JSONAPI\Resource(idFetcher: 'id')
class People {
    public string $id
```

## Attributes
PHP attribute `JSONAPI\Resource\Attributes\Attribute` is used for setting up resource [attributes](https://jsonapi.org/format/#document-resource-object-attributes).
Attribute can be assigned on public property, method or class.

::: tip
Please do not mix up PHP attributes with JSON:API resource attributes.
:::

### Key
First parameter in attribute constructor is string, and it is resource attribute key.
If key not provided or null, it will be the property or method name.
For class attribute key is required.

### Value
Attribute value fetcher depends on what attribute target.

#### Property target
Class property must be public, so the value can be fetched from the property.

```php{1}
#[JSONAPI\Attribute()]
public string $name
```

#### Method target
Method must be public, so we can fetch value from it.

```php{1}
#[JSONAPI\Attribute()]
public function name(): string
```

#### Class target
Attribute can be assigned on class, in this case `key` and `value` must be provided to attribute constructor.
Value can be some static value or anonymous function, that can be called to fetch dynamic value.
```php{1-4}
#[Attribute(
    key: 'name',
    value: fn($resource) => $resource->getDynamicName()
)]
class ClassWithDynamicName {
  public function getDynamicName(): string
```

## Relationships
PHP attribute `JSONAPI\Resource\Attributes\Relationship` is used for mapping [resource relationships](https://jsonapi.org/format/#document-resource-object-relationships).

`Relationship` attribute extends same base abstract class as `Attribute`.
You can reference to [Attributes](/guide/attributes/#attributes) to see how to set up `key` and `value`.
