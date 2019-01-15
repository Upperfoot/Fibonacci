<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Exceptions\InvalidStateException;
use App\Service\Fibonacci\WriterService;
use Illuminate\Config\Repository;
use Illuminate\Console\Command;

/**
 * Class FibonacciCalculatorWorkerCommand
 *
 * Calculate Fibonacci series using iteration
 *
 * @author PB
 */
class FibonacciCalculatorWorkerCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'fibonacci:generate';

    /**
     * @var string
     */
    protected $description = 'Generates fibonacci sequence iteratively';

    /**
     * @var WriterService
     */
    protected $fibonacciWriterService;

    /**
     * @var int
     */
    protected $sleepTime;

    /**
     * @param WriterService $fibonacciWriterService
     * @param Repository $configRepository
     * @author PB
     */
    public function __construct(WriterService $fibonacciWriterService, Repository $configRepository)
    {
        parent::__construct();

        $this->fibonacciWriterService = $fibonacciWriterService;
        $this->sleepTime = (int) ($configRepository->get('fibonacci.worker_sleep_time_ns') ?? 100000);
    }

    /**
     * Calculate fibonacci numbers using iteration
     * Number addition and comparision is done using BCMath in order to avoid integer overflow
     * F(92) is the maximum to calculate before hitting PHP_INT_MAX 64bit limit
     * Which results in integer overflow
     *
     * @throws InvalidStateException
     * @author PB
     */
    public function handle(): void
    {
        if (false === $this->fibonacciWriterService->canContinue()) {
            return ;
        }

        $this->fibonacciWriterService->initialiseSequence();

        do {
            $latestTwoFibonacciNumbers = $this->fibonacciWriterService->getLastNumbersFromSequence(2);
            if (bccomp($latestTwoFibonacciNumbers[0], $latestTwoFibonacciNumbers[1]) === 1) {
                throw new InvalidStateException('Fibonacci sequence is in invalid state');
            }

            $nextFibonacciNumber = bcadd($latestTwoFibonacciNumbers[0], $latestTwoFibonacciNumbers[1]);
            $this->fibonacciWriterService->addToSequence($nextFibonacciNumber);

            usleep($this->sleepTime);

        } while ($this->fibonacciWriterService->canContinue());
    }
}
