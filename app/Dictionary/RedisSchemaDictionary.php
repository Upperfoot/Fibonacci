<?php

declare(strict_types=1);

namespace App\Dictionary;

/**
 * Class RedisSchemaDictionary
 * This class keeps the configuration dictionary in order to use redis list
 *
 * @author PB
 */
class RedisSchemaDictionary
{
    /**
     * @var int
     */
    private $redisElementsMax = 4294967295;

    /**
     * @var string
     */
    private $fibonacciListName = 'fibonacci:seq';

    /**
     * Return the list name to store fibonacci sequences
     *
     * @return string
     * @author PB
     */
    public function getFibonacciListName(): string
    {
        return $this->fibonacciListName;
    }

    /**
     * Return the max elements redis can handle in a list
     *
     * @return int
     * @author PB
     */
    public function getRedisElementsMax(): int
    {
        return $this->redisElementsMax;
    }
}