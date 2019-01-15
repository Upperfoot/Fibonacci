<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Console\Commands\FibonacciCalculatorWorkerCommand;
use App\Exceptions\InvalidStateException;
use App\Service\Fibonacci\WriterService;
use Illuminate\Config\Repository;
use Tests\TestCase;

/**
 * Class FibonacciCalculatorWorkerCommandTest
 * @author PB
 */
class FibonacciCalculatorWorkerCommandTest extends TestCase
{
    /**
     * @author PB
     */
    public function testFibonacciCaluculation()
    {
        $mockConfigRepository = $this->getMockBuilder(Repository::class)->getMock();
        $mockConfigRepository->method('get')
            ->willReturn(0);

        $writerServiceStub = $this->getMockBuilder(WriterService::class)
            ->setMethods(['initialiseSequence', 'addToSequence', 'canContinue', 'getLastNumbersFromSequence'])
            ->disableOriginalConstructor()
            ->getMock();

        $writerServiceStub->method('canContinue')->willReturn(true);

        $writerServiceStub->method('getLastNumbersFromSequence')->willReturn([
            '55',
            '89',
        ]);

        $writerServiceStub->method('addToSequence')
            ->with(144)
            ->willThrowException(new \Exception('exiting'));

        $fibonacciCalculatorWorkerCommand = \Mockery::mock(
            'App\Console\Commands\FibonacciCalculatorWorkerCommand[]', [
                $writerServiceStub,
                $mockConfigRepository
            ]
        );

        $this->expectException(\Exception::class);
        $fibonacciCalculatorWorkerCommand->handle();
    }

    /**
     * @author PB
     */
    public function testFibonacciCaluculationInvalidState()
    {
        $mockConfigRepository = $this->getMockBuilder(Repository::class)->getMock();
        $mockConfigRepository->method('get')
            ->willReturn(0);

        $writerServiceStub = $this->getMockBuilder(WriterService::class)
            ->setMethods(['initialiseSequence', 'addToSequence', 'canContinue', 'getLastNumbersFromSequence'])
            ->disableOriginalConstructor()
            ->getMock();

        $writerServiceStub->method('canContinue')->willReturn(true);

        $writerServiceStub->method('getLastNumbersFromSequence')->willReturn([
            '95',
            '89',
        ]);

        $writerServiceStub->method('addToSequence')
            ->with(144)
            ->willThrowException(new \Exception('exiting'));

        $fibonacciCalculatorWorkerCommand = \Mockery::mock(
            'App\Console\Commands\FibonacciCalculatorWorkerCommand[]', [
                $writerServiceStub,
                $mockConfigRepository
            ]
        );

        $this->expectException(InvalidStateException::class);
        $fibonacciCalculatorWorkerCommand->handle();
    }
}
