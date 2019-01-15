<?php

namespace Tests\Unit\Service\Fibonacci;

use App\Dictionary\RedisSchemaDictionary;
use App\Service\Fibonacci\WriterService;
use Illuminate\Config\Repository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WriterServiceTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $writerServiceStub;

    /**
     * @author PB
     */
    protected function setUp()
    {
        $this->writerServiceStub = $this->getMockBuilder(WriterService::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @author PB
     */
    protected function tearDown()
    {
        $this->writerServiceStub = null;
    }

    /**
     * @author PB
     */
    public function testInitialiseSequence()
    {
        $mockRedisClient = $this->getMockBuilder(\Redis::class)->getMock();
        $mockRedisClient->expects($this->exactly(2))
            ->method('rPush')
            ->withAnyParameters();

        $mockRedisSchemaDictionary = $this->getMockBuilder(RedisSchemaDictionary::class)
            ->getMock();
        $mockRedisSchemaDictionary->expects($this->exactly(2))
            ->method('getFibonacciListName')
            ->willReturn('fibonacci:test');

        $writerServiceStubReflection = new \ReflectionClass(WriterService::class);
        $propertyRedisClient = $writerServiceStubReflection->getProperty('redisClient');
        $propertyRedisClient->setAccessible(true);
        $propertyRedisClient->setValue($this->writerServiceStub, $mockRedisClient);

        $propertyRedisSchemaDictionary = $writerServiceStubReflection->getProperty('redisSchemaDictionary');
        $propertyRedisSchemaDictionary->setAccessible(true);
        $propertyRedisSchemaDictionary->setValue($this->writerServiceStub, $mockRedisSchemaDictionary);

        $writerServiceStubReflection->getMethod('initialiseSequence')->invoke($this->writerServiceStub);
    }

    /**
     * @author PB
     */
    public function testAddToSequence()
    {
        $mockRedisClient = $this->getMockBuilder(\Redis::class)->getMock();
        $mockRedisClient->expects($this->once())
            ->method('rPush')
            ->with('fibonacci:test', '12345678');

        $mockRedisSchemaDictionary = $this->getMockBuilder(RedisSchemaDictionary::class)
            ->getMock();
        $mockRedisSchemaDictionary->expects($this->once())
            ->method('getFibonacciListName')
            ->willReturn('fibonacci:test');

        $writerServiceStubReflection = new \ReflectionClass(WriterService::class);
        $propertyRedisClient = $writerServiceStubReflection->getProperty('redisClient');
        $propertyRedisClient->setAccessible(true);
        $propertyRedisClient->setValue($this->writerServiceStub, $mockRedisClient);

        $propertyRedisSchemaDictionary = $writerServiceStubReflection->getProperty('redisSchemaDictionary');
        $propertyRedisSchemaDictionary->setAccessible(true);
        $propertyRedisSchemaDictionary->setValue($this->writerServiceStub, $mockRedisSchemaDictionary);

        $writerServiceStubReflection->getMethod('addToSequence')->invokeArgs($this->writerServiceStub, ['12345678']);
    }

    /**
     * @dataProvider canContinueValueProvider
     * @author PB
     */
    public function testCanContinue($configRepositoryReturn, $redisDictionaryReturn, $sequenceLength, $expected)
    {
        $mockConfigRepository = $this->getMockBuilder(Repository::class)->getMock();
        $mockConfigRepository->expects($this->once())
            ->method('get')
            ->willReturn($configRepositoryReturn);

        $mockRedisSchemaDictionary = $this->getMockBuilder(RedisSchemaDictionary::class)
            ->getMock();
        $mockRedisSchemaDictionary
            ->method('getRedisElementsMax')
            ->willReturn($redisDictionaryReturn);

        $this->writerServiceStub
            ->expects($this->once())
            ->method('getSequenceLength')
            ->willReturn($sequenceLength);

        $writerServiceStubReflection = new \ReflectionClass(WriterService::class);
        $propertyRedisSchemaDictionary = $writerServiceStubReflection->getProperty('redisSchemaDictionary');
        $propertyRedisSchemaDictionary->setAccessible(true);
        $propertyRedisSchemaDictionary->setValue($this->writerServiceStub, $mockRedisSchemaDictionary);

        $propertyConfigRepository = $writerServiceStubReflection->getProperty('configRepository');
        $propertyConfigRepository->setAccessible(true);
        $propertyConfigRepository->setValue($this->writerServiceStub, $mockConfigRepository);

        $this->assertEquals(
            $expected,
            $writerServiceStubReflection->getMethod('canContinue')->invoke($this->writerServiceStub)
        );
    }

    /**
     * @return array
     * @author PB
     */
    public function canContinueValueProvider()
    {
        // [$configRepositoryReturn, $redisDictionaryReturn, $sequenceLength, $expected]
        return [
            [null, 50, 20, true],
            [50, null , 20, true],
            [50, null , 200, false],
            [50, 2000 , 200, false]
        ];
    }
}
