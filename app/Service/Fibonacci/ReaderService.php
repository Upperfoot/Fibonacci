<?php

declare(strict_types=1);

namespace App\Service\Fibonacci;

use App\Exceptions\NumberFormatException;

/**
 * Class ReaderService
 * Provides an interface to redis lists to read and lookup
 * Fibonacci sequence
 *
 * @author PB
 */
class ReaderService extends AbstractService
{
    /**
     * Return elements from end of the redis list
     *
     * @param int $limit
     * @return array
     * @author PB
     */
    public function getSequenceTail(int $limit = 15): array
    {
        $output = [];
        $totalElements = $this->getSequenceLength();
        $fibonacciSequence = $this->getLastNumbersFromSequence($limit);

        foreach (array_reverse($fibonacciSequence) as $index => $fibonacci) {
            $output[$totalElements - $index] = $fibonacci;
        }

        return $output;
    }

    /**
     * Return the specific sequence range from redis list
     *
     * @param int $start
     * @param int $end
     * @return array
     * @throws NumberFormatException
     * @author PB
     */
    public function getSequenceRange(int $start, int $end): array
    {
        $sequenceLength = $this->getSequenceLength();

        if ($start < 0 || $end < 0 || $end < $start) {
            throw new NumberFormatException('Start and ending index should be positive and valid');
        }

        if ($start > $sequenceLength || $end > $sequenceLength) {
            throw new NumberFormatException(sprintf(
                'Start and ending index can not be bigger than current sequence size (%d)',
                $sequenceLength
            ));
        }

        $sequenceRange = $this->redisClient->lRange(
            $this->redisSchemaDictionary->getFibonacciListName(), $start, $end
        );

        return array_combine(range($start, $end), $sequenceRange);
    }
}
