<?php namespace JSONAPI\Resource\Cache;

use Psr\SimpleCache\InvalidArgumentException;

class InvalidKeyException extends \InvalidArgumentException implements InvalidArgumentException
{
}