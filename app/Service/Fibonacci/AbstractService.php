<?php

declare(strict_types=1);

namespace App\Service\Fibonacci;

use App\Dictionary\RedisSchemaDictionary;
use Illuminate\Config\Repository;
use Illuminate\Redis\RedisManager;

/**
 * Class AbstractFibonacciService
 *
 * Provide dependencies and helper methods to read and write
 * Fibonacci sequence in redis
 *
 * @author PB
 */
abstract class AbstractService
{
    /**
     * @var \Redis $redisClient
     */
    protected $redisClient;

    /**
     * @var RedisSchemaDictionary $redisSchemaDictionary
     */
    protected $redisSchemaDictionary;

    /**
     * @var Repository
     */
    protected $configRepository;

    /**
     * @param RedisManager $redisManager
     * @param RedisSchemaDictionary $redisSchemaDictionary
     * @param Repository $configRepository
     * @author PB
     */
    public function __construct(
        RedisManager $redisManager,
        RedisSchemaDictionary $redisSchemaDictionary,
        Repository $configRepository
    )
    {
        $this->redisClient = $redisManager->client();
        $this->redisSchemaDictionary = $redisSchemaDictionary;
        $this->configRepository = $configRepository;
    }

    /**
     * Return the number of elements in redis list
     *
     * @return int
     * @author PB
     */
    public function getSequenceLength(): int
    {
        return $this->redisClient->lLen(
            $this->redisSchemaDictionary->getFibonacciListName()
        );
    }

    /**
     * Return the last N number from the redis list
     *
     * @param int $limit
     * @return array
     * @author PB
     */
    public function getLastNumbersFromSequence(int $limit = 15): array
    {
        return $this->redisClient->lRange(
            $this->redisSchemaDictionary->getFibonacciListName(), -$limit, -1
        );
    }
}
