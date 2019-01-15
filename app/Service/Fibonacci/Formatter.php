<?php

declare(strict_types=1);

namespace App\Service\Fibonacci;

/***
 * Class Formatter
 *
 * Provides helpers to format the sequence output from
 * Redis list in a human friendly way
 *
 * @author PB
 */
class Formatter
{
    /**
     * Return the sequence output with trimmed integers
     *
     * @param array $sequence
     * @return array
     * @author PB
     */
    public function formatArray(array $sequence): array
    {
        $output = [];
        foreach ($sequence as $index => $fibonacci) {
            if (strlen($fibonacci) > 65) {
                $output[] = [$index, sprintf('INTEGER (%s)', strlen($fibonacci))];
                continue;
            }

            $output[] = [$index, $fibonacci];
        }

        return $output;
    }

    /**
     * Sort an array in ascending order
     *
     * @param array $sequence
     * @return array
     * @author PB
     */
    public function sortArrayAscending(array $sequence): array
    {
        ksort($sequence, SORT_NUMERIC);

        return $sequence;
    }

    /**
     * Sort an array in descending order
     *
     * @param array $sequence
     * @return array
     * @author PB
     */
    public function sortArrayDescending(array $sequence): array
    {
        krsort($sequence, SORT_NUMERIC);

        return $sequence;
    }
}
