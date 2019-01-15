<?php

declare(strict_types=1);

namespace App\Service\Fibonacci;

/**
 * Class WriterService
 * Provide an interface to redis lists to
 * Store Fibonacci sequence
 *
 * @author PB
 */
class WriterService extends AbstractService
{
    /**
     * Initialise the redis list with first two numbers
     *
     * @author PB
     */
    public function initialiseSequence(): void
    {
        if ($this->getSequenceLength() < 2) {
            $this->redisClient->rPush($this->redisSchemaDictionary->getFibonacciListName(), 0);
            $this->redisClient->rPush($this->redisSchemaDictionary->getFibonacciListName(), 1);
        }
    }

    /**
     * Append a calculated fibonacci number to redis list
     *
     * @param string $fibonacci
     * @author PB
     */
    public function addToSequence(string $fibonacci): void
    {
        $this->redisClient->rPush(
            $this->redisSchemaDictionary->getFibonacciListName(),
            $fibonacci
        );
    }

    /**
     * Compare the elements in the list with maximum number of elements redis can support
     * return true/false to specify if further iteration is possible or not
     *
     * @return bool
     * @author PB
     */
    public function canContinue(): bool
    {
        $limit =
            (int) ($this->configRepository->get('fibonacci.max_fibonacci_elements')
                ?? $this->redisSchemaDictionary->getRedisElementsMax());

        return $this->getSequenceLength() < $limit;
    }

}
