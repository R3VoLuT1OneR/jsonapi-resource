<h2 align="center">JSON:API Resource</h2>
<p align="center">PHP 8.0 Library for parsing JSON:API resource in to data response.</p>
<p align="center">
<a href="https://packagist.org/packages/pz/jsonapi-resource"><img src="https://img.shields.io/packagist/php-v/pz/jsonapi-resource" /></a>
<a href="https://phpstan.org"><img src="https://img.shields.io/badge/PHPStan-level%206-brightgreen.svg?style=flat" /></a>
<a href="https://sonarcloud.io/dashboard?id=R3VoLuT1OneR_jsonapi-resource"><img src="https://sonarcloud.io/api/project_badges/measure?project=R3VoLuT1OneR_jsonapi-resource&metric=alert_status" /></a>
<a href="https://scrutinizer-ci.com/g/R3VoLuT1OneR/jsonapi-resource/?branch=main"><img src="https://scrutinizer-ci.com/g/R3VoLuT1OneR/jsonapi-resource/badges/quality-score.png?b=main" /></a>
</p>

## Installation
```shell
composer require pz/jsonapi-resource
```

## Documentation
- [Library Documentation](https://r3volut1oner.github.io/jsonapi-resource/)
- [JSON:API Documentation](https://jsonapi.org)

## TODO
List of ideas and tasks must be implemented in the future.

- [ ] Feat: Resource `meta` PHP attribute
- [ ] Feat: Hydration or deserialization for resource creation or update.
- [ ] Docs: Metadata guide
- [ ] Docs: Full API Reference
- [ ] Research: Possible performance improvements.

## Development
There are `Dockerfile` and `docker-compose` config needed for developing the library on same environment configurations.
We suggest using docker-compose for the library development.

### Docker-compose local configurations
Please copy `docker-compose.override-example.yaml` to `docker-compose.override.yaml` and set proper user UID and desired build type.

First we need to build the docker image.

```shell
docker-compose build app
```

Lets install composer dependencies from the container.
```shell
docker-compose run app composer install
```

## Testing
We are using [PestPHP](https://pestphp.com/) for unit testing the code, so please read about before test.
```shell
docker-compose run app pest
```

For syntax verification and checks we use [PHPSTan](https://github.com/phpstan/phpstan).
```shell
docker-compose run app phpstan
```

## Contributing
Fill free to create issues and send merge requests.

Before sending merge request please run `phpstan` and `pest`.