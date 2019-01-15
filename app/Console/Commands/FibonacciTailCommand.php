<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Service\Fibonacci\Formatter;
use App\Service\Fibonacci\ReaderService;
use Illuminate\Console\Command;

/**
 * Class FibonacciTailCommand
 *
 * Output last 15 numbers in sequence
 * @author PB
 */
class FibonacciTailCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'fibonacci:tail
                        {--num=15 : Output last NUM instead of the last 15}';
    /**
     * @var ReaderService
     */
    protected $fibonacciReaderService;

    /**
     * @var Formatter
     */
    protected $fibonacciFormatter;

    /**
     * @param ReaderService $fibonacciReaderService
     * @param Formatter $fibonacciFormatter
     * @author PB
     */
    public function __construct(ReaderService $fibonacciReaderService, Formatter $fibonacciFormatter)
    {
        parent::__construct();

        $this->fibonacciReaderService = $fibonacciReaderService;
        $this->fibonacciFormatter = $fibonacciFormatter;
    }

    /**
     * Tail sequence and output the numbers
     *
     * @author PB
     */
    public function handle(): void
    {
        $lineLimit = 15;

        if ($this->option('num') !== null) {
            $lineLimit = filter_var($this->option('num'), FILTER_VALIDATE_INT);
        }

        do {
            $this->output->write(sprintf("\033\143"));

            $rows = $this->fibonacciFormatter->formatArray(
                $this->fibonacciReaderService->getSequenceTail($lineLimit)
            );

            $this->output->table(['F(n)', 'Number'], $rows);

            usleep(200000);
        } while (true);
    }
}
