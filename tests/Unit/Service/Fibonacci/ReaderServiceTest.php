<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Fibonacci;

use App\Dictionary\RedisSchemaDictionary;
use App\Exceptions\NumberFormatException;
use App\Service\Fibonacci\ReaderService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ReaderServiceTest
 * @author PB
 */
class ReaderServiceTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $readerServiceStub;

    /**
     * @author PB
     */
    protected function setUp()
    {
        $this->readerServiceStub = $this->getMockBuilder(ReaderService::class)
            ->setMethods(['getSequenceLength', 'getLastNumbersFromSequence'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @author PB
     */
    protected function tearDown()
    {
        $this->readerServiceStub = null;
    }

    /**
     * @author PB
     */
    public function testGetSequenceTail()
    {
        $this->readerServiceStub->expects($this->once())
            ->method('getSequenceLength')
            ->willReturn(15);

        $this->readerServiceStub->expects($this->once())
            ->method('getLastNumbersFromSequence')
            ->with('5')
            ->willReturn([
                55,
                89,
                144,
                233,
                377,
                610,
            ]);

        $sequenceTail = $this->readerServiceStub->getSequenceTail(5);

        $this->assertIsArray($sequenceTail);
        $this->assertEquals([
            10 => 55,
            11 => 89,
            12 => 144,
            13 => 233,
            14 => 377,
            15 => 610,
        ], $sequenceTail);
    }

    /**
     * @author PB
     */
    public function testGetSequenceTailWithEmptySequence()
    {
        $this->readerServiceStub->expects($this->once())
            ->method('getSequenceLength')
            ->willReturn(0);

        $this->readerServiceStub->expects($this->once())
            ->method('getLastNumbersFromSequence')
            ->with('5')
            ->willReturn([]);

        $sequenceTail = $this->readerServiceStub->getSequenceTail(5);

        $this->assertIsArray($sequenceTail);
        $this->assertEquals([], $sequenceTail);
    }

    /**
     * @author PB
     */
    public function testGetSequenceRangeWithInvalidStartAndEnd()
    {
        $this->readerServiceStub->expects($this->once())
            ->method('getSequenceLength')
            ->willReturn(50);

        $this->expectException(NumberFormatException::class);
        $this->expectExceptionMessage('Start and ending index should be positive and valid');

        $this->readerServiceStub->getSequenceRange(25, 5);
    }

    /**
     * @author PB
     */
    public function testGetSequenceRangeWithIndexBiggerThanListLength()
    {
        $this->readerServiceStub->expects($this->once())
            ->method('getSequenceLength')
            ->willReturn(15);

        $this->expectException(NumberFormatException::class);
        $this->expectExceptionMessage('Start and ending index can not be bigger than current sequence size (15)');

        $this->readerServiceStub->getSequenceRange(5, 25);
    }

    /**
     * @author PB
     */
    public function testGetSequenceRangeWithValidArgs()
    {
        $this->readerServiceStub->expects($this->once())
            ->method('getSequenceLength')
            ->willReturn(15);

        $mockRedisSchemaDictionary = $this->getMockBuilder(RedisSchemaDictionary::class)
            ->getMock();
        $mockRedisSchemaDictionary
            ->method('getFibonacciListName')
            ->willReturn('fibonacci:test');

        $mockRedisClient = $this->getMockBuilder(\Redis::class)->getMock();
        $mockRedisClient->expects($this->once())
            ->method('lRange')
            ->with('fibonacci:test', 10, 15)
            ->willReturn([
                55,
                89,
                144,
                233,
                377,
                610,
            ]);

        $readerServiceStubReflection = new \ReflectionClass(ReaderService::class);
        $propertyRedisClient = $readerServiceStubReflection->getProperty('redisClient');
        $propertyRedisClient->setAccessible(true);
        $propertyRedisClient->setValue($this->readerServiceStub, $mockRedisClient);

        $propertyRedisSchemaDictionary = $readerServiceStubReflection->getProperty('redisSchemaDictionary');
        $propertyRedisSchemaDictionary->setAccessible(true);
        $propertyRedisSchemaDictionary->setValue($this->readerServiceStub, $mockRedisSchemaDictionary);

        $outputSequenceRange = $readerServiceStubReflection->getMethod('getSequenceRange')
            ->invokeArgs($this->readerServiceStub, [10, 15]);

        $this->assertIsArray($outputSequenceRange);
        $this->assertEquals([
            10 => 55,
            11 => 89,
            12 => 144,
            13 => 233,
            14 => 377,
            15 => 610,
        ], $outputSequenceRange);
    }
}
