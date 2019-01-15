<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Service\Fibonacci\Formatter;
use App\Service\Fibonacci\ReaderService;
use Illuminate\Console\Command;

/**
 * Class FibonacciQueryCommand
 * Allows filtering and sorting fibonacci sequence
 *
 * @author PB
 */
class FibonacciQueryCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'fibonacci:query
                        {--from=0 : Starting index}
                        {--to=20 : Ending index}
                        {--sort=asc : Order the output [asc/desc]}';
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
     */
    public function __construct(ReaderService $fibonacciReaderService, Formatter $fibonacciFormatter)
    {
        parent::__construct();

        $this->fibonacciReaderService = $fibonacciReaderService;
        $this->fibonacciFormatter = $fibonacciFormatter;
    }

    /**
     * Filter the fibonacci sequence, optionally can sort it and output it
     *
     * @author PB
     */
    public function handle(): void
    {
        $lookupStartingIndex = (int) $this->option('from');
        $lookupEndingIndex = (int) $this->option('to');

        $output = $this->fibonacciReaderService->getSequenceRange($lookupStartingIndex, $lookupEndingIndex);
        $formattedOutput = $this->fibonacciFormatter->formatArray($output);

        $sortedOutput = $this->option('sort') === 'asc' ?
                $this->fibonacciFormatter->sortArrayAscending($formattedOutput) :
                $this->fibonacciFormatter->sortArrayDescending($formattedOutput);

        $this->output->table(['F(n)', 'Number'],
            empty($sortedOutput) ? [['-', '-']] : $sortedOutput
        );

        $this->output->writeln(sprintf('Sequence Length: [%d] ', $this->fibonacciReaderService->getSequenceLength()));
    }
}
