<?php namespace JSONAPI\Resource\Cache;

use Psr\SimpleCache\InvalidArgumentException;

class InvalidTTLException extends \InvalidArgumentException implements InvalidArgumentException
{
}