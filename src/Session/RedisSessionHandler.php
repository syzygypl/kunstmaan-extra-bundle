<?php

namespace ArsThanea\KunstmaanExtraBundle\Session;

class RedisSessionHandler implements \SessionHandlerInterface
{
    /**
     * @var \Redis
     */
    private $redis;

    /**
     * @var string
     */
    private $keyPrefix;

    /**
     * @var int
     */
    private $maxLifetime;

    public function __construct(\Redis $redis, $keyPrefix = 'session:')
    {
        $this->redis = $redis;

        $this->keyPrefix = $keyPrefix;

        $this->maxLifetime = ini_get('session.gc_maxlifetime');
    }

    public function open($savePath, $name)
    {
    }

    public function gc($maxLifetime)
    {
    }

    public function close()
    {
    }

    public function destroy($sessionId)
    {
        $this->redis->del($this->keyPrefix . $sessionId);
    }

    /**
     * Read the session data from Redis.
     *
     * @param  string $sessionId The session id.
     * @return string            The serialized session data.
     */
    public function read($sessionId)
    {
        $sessionId = $this->keyPrefix . $sessionId;
        $sessionData = $this->redis->get($sessionId);

        $this->redis->expire($sessionId, $this->maxLifetime);

        return $sessionData;
    }

    public function write($sessionId, $sessionData)
    {
        $sessionId = $this->keyPrefix . $sessionId;

        $this->redis->set($sessionId, $sessionData);

        $this->redis->expire($sessionId, $this->maxLifetime);
    }
}
