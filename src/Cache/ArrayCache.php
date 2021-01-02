<?php namespace JSONAPI\Resource\Cache;

use Psr\SimpleCache\CacheInterface;

use Exception;
use DateInterval;
use DateTime;

/**
 * Cache implementation that can be used for metadata caching.
 */
class ArrayCache implements CacheInterface
{
    /** @var array<string, mixed> */
    private array $store = [];

    /** @var array<string, DateTime> */
    private array $storeExpire = [];

    public function set($key, $value, $ttl = null)
    {
        $this->verifyKey($key);

        $this->store[$key] = $value;

        if (null !== ($expireAt = $this->buildExpireData($ttl))) {
            $this->storeExpire[$key] = $expireAt;
        }

        return true;
    }

    /**
     * @param iterable<mixed> $values
     */
    public function setMultiple($values, $ttl = null)
    {
        if (!is_array($values)) {
            throw new InvalidKeyException('Key => value map must be an array', 3);
        }

        return array_reduce(
            array_keys($values),
            fn($result, $key) => $result && $this->set($key, $values[$key], $ttl),
            true
        );
    }

    public function get($key, $default = null)
    {
        $this->verifyKey($key);
        $this->verifyExpired($key);

        if (isset($this->store[$key])) {
            return $this->store[$key];
        }

        return $default;
    }

    /**
     * @param iterable<string> $keys
     * @return mixed[]
     */
    public function getMultiple($keys, $default = null)
    {
        if (!is_array($keys)) {
            throw new InvalidKeyException('Keys must be an array', 3);
        }

        return array_map(fn($key) => $this->get($key, $default), $keys);
    }

    public function has($key)
    {
        $this->verifyKey($key);
        $this->verifyExpired($key);

        return isset($this->store[$key]);
    }

    public function delete($key)
    {
        $this->verifyKey($key);

        unset($this->store[$key]);
        unset($this->storeExpire[$key]);

        return true;
    }

    /**
     * @param iterable<string> $keys
     */
    public function deleteMultiple($keys)
    {
        if (!is_array($keys)) {
            throw new InvalidKeyException('Keys must be an array', 3);
        }

        return array_reduce(
            $keys,
            fn($result, $key) => $result && $this->delete($key),
            true
        );
    }

    public function clear(): bool
    {
        $this->store = [];
        $this->storeExpire = [];

        return true;
    }

    protected function verifyExpired(string $key): void
    {
        if (isset($this->storeExpire[$key]) && $this->now() > $this->storeExpire[$key]) {
            unset($this->store[$key]);
            unset($this->storeExpire[$key]);
        }
    }

    /**
     * @param null|int|DateInterval $ttl
     * @return DateTime|null
     *
     * @throws InvalidTTLException
     */
    protected function buildExpireData($ttl = null): DateTime | null
    {
        if (is_null($ttl)) {
            return null;
        }

        if (is_integer($ttl)) {
            $ttl = new DateInterval(sprintf('PT%sS', (int) $ttl));
        }

        if (!$ttl instanceof DateInterval) {
            throw new InvalidTTLException('Failed to parse provided ttl');
        }

        return $this->now()->add($ttl);
    }

    protected function verifyKey(mixed $key): string
    {
        if (!is_string($key)) {
            throw new InvalidKeyException('Key is not string', 2);
        }

        return $key;
    }

    protected function now(): DateTime
    {
        return new DateTime('now');
    }
}