<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Console\Commands\FibonacciTailCommand;
use App\Service\Fibonacci\Formatter;
use App\Service\Fibonacci\ReaderService;
use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Tests\TestCase;

/**
 * Class FibonacciTailCommandTest
 * @author PB
 */
class FibonacciTailCommandTest extends TestCase
{
    /**
     * @author PB
     */
    public function testFibonacciTailCommand()
    {
        $fibonacciFormatter = new Formatter();

        $readerServiceStub = $this->getMockBuilder(ReaderService::class)
            ->setMethods(['getSequenceTail', 'getSequenceLength', 'getLastNumbersFromSequence'])
            ->disableOriginalConstructor()
            ->getMock();

        $readerServiceStub->method('getSequenceTail')->willReturn([
            10 => '55',
            11 => '89',
            12 => '144',
            13 => '233',
            14 => '377',
            15 => '610',
        ]);

        $fibonacciTailCommand = \Mockery::mock(
            'App\Console\Commands\FibonacciTailCommand[num]', [
                $readerServiceStub,
                $fibonacciFormatter
            ]
        );

        $inputStub = $this->getMockBuilder(ArgvInput::class)->setMethods(['getOption'])->getMock();
        $outputStub = $this->getMockBuilder(OutputStyle::class)
            ->disableOriginalConstructor()
            ->setMethods(['table', 'write'])
            ->getMock();

        $outputStub->method('table')->with(['F(n)', 'Number',])
            ->willThrowException(new \Exception('exiting'));

        $fibonacciTailCommandReflection = new \ReflectionClass(FibonacciTailCommand::class);

        $propertyInput = $fibonacciTailCommandReflection->getProperty('input');
        $propertyInput->setAccessible(true);
        $propertyInput->setValue($fibonacciTailCommand, $inputStub);
        $propertyOutput = $fibonacciTailCommandReflection->getProperty('output');
        $propertyOutput->setAccessible(true);
        $propertyOutput->setValue($fibonacciTailCommand, $outputStub);

        $fibonacciTailCommand->shouldReceive('option')->andReturn(5);

        $this->expectException(\Exception::class);
        $fibonacciTailCommand->handle();
    }
}