# Getting Started

## System Requirements
You need PHP >= 8.0 to use `pz/jsonapi-resource`.

## Installation
Library is available on [Packagist](https://packagist.org/) and can be installed using [Composer](https://getcomposer.org/).
```shell
composer require pz/jsonapi-resource
```

Most modern frameworks will include the Composer autoloader by default, but ensure the following file is included:

```php
<?php
// Include the Composer autoloader
require 'vendor/autoload.php';
```

## Mapping
::: warning
User of the library must be at least familiar with the JSON:API [specifications](https://jsonapi.org/format/).
:::

Let's use example similar to example on JSON:API website. We will have "articles" resource with "author" relationship that is "people" resource type.
<details>
    <summary>Articles.php</summary>

<<< @/examples/Articles.php
</details>
<details>
    <summary>People.php</summary>

<<< @/examples/People.php
</details>

::: tip
To use PHP attributes and make our code clear import attributes namespace and give it alias.
```php
use JSONAPI\Resource\Attributes as JSONAPI;
```
:::

### Resource Identification

PHP attribute `JSONAPI\Resource\Attributes\Resource` is used for setup [resource identification](https://jsonapi.org/format/#document-resource-object-identification).
First argument to attribute constructor required and must be `string` that represent resource `type`.
Next params used to set up resource `id` fetcher, by default it is the `getId()` method that exists in our classes.

Then we can assign `Resource` attribute to our class and provide resource type next way:
```php{1}
#[JSONAPI\Resource('articles')]
class Articles
```
```php{1}
#[JSONAPI\Resource('people')]
class People
```

### Attributes
PHP attribute `JSONAPI\Resource\Attributes\Attribute` is used for setting up resource [attributes](https://jsonapi.org/format/#document-resource-object-attributes).

We have 1 attribute in "articles" resource, that is "title" and it is value fetched with `getTitle()` method. So we append PHP attribute to that method.
```php{1}
#[JSONAPI\Attribute()]
public function getTitle(): string
```

There are 2 attributes in our "people" resource `name` and `age`. Value for that attributes can be fetched accordingly by `getName()` and `getAge()` methods.
```php{1}
#[JSONAPI\Attribute('name')]
public function getName(): string
```
```php{1}
#[JSONAPI\Attribute('age')]
public function getAge(): int
```

### Relationships
`JSONAPI\Resource\Attributes\Relationship` is used for setting up resource [relationships](https://jsonapi.org/format/#document-resource-object-relationships).

Our "articles" have 1 relationship `author`, that is list of other persons. Value for the relationship can be fetched by `getAuthor()` method.
```php{1}
#[JSONAPI\Relationship('author')]
public function getAuthor(): ?People
```

## Serialization
The most basic and most required usage of the mapping is serialization. 
Create a new person and serialize it with `JSONAPI\Resource\Serializer`.

### Simple example
This example is response to `GET /people/1` JSON:API request.

<<< @/examples/serialize-simple.php{9-11}

Output

<<< @/examples/simple.json

### Fieldset
`JSONAPI\Resource\Feildset` represents requested [fieldset](https://jsonapi.org/format/#fetching-sparse-fieldsets). It is provided by request query string param `field`.

```php
$fields = $httpMessage->getQueryParams()['fields'];
$fieldSet = new Fieldset($fields);
```

### Fetching includes
`JSONAPI\Resrouce\Includeset` represents requested [related resources](https://jsonapi.org/format/#fetching-includes) (relationships). It is provided by request query string param `include`.

```php
$include = $httpMessage->getQueryParams()['include'];
$includeSet = Includeset::fromString(include);
```

### Example with "include" and "fields"
Let's create much more complicated request example, we want to get all articles with an `author`'s `age`.

HTTP request by JSON:API specification will look like this:
```http request
GET /articles?include=author&fields[people]=age
```

<<< @/examples/serialize-include.php{21-36}

<details>
<summary>Output</summary>

<<< @/examples/include.json
</details>

