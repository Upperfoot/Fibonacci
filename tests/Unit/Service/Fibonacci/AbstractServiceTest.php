<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Fibonacci;

use App\Dictionary\RedisSchemaDictionary;
use App\Service\Fibonacci\AbstractService;
use Illuminate\Config\Repository;
use Illuminate\Redis\RedisManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractServiceTest
 * @author PB
 */
class AbstractServiceTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $abstractServiceStub;

    /**
     * @author PB
     */
    protected function setUp()
    {
        $redisSpy = new class {
            public function lLen($listName) {
                return ($listName === 'fibonacci:test') ? 7 : 0;
            }
            public function lRange($listName, $start, $end) {
                return ($listName === 'fibonacci:test') ? [0,1,1,2,3,5,8] : [];
            }
        };

        $redisManagerStub = $this->getMockBuilder(RedisManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['client'])
            ->getMock();

        $redisManagerStub->method('client')
            ->willReturn($redisSpy);

        $redisSchemaDictionaryStub = $this->getMockBuilder(RedisSchemaDictionary::class)
            ->getMock();

        $redisSchemaDictionaryStub->method('getFibonacciListName')
            ->willReturn('fibonacci:test');

        $configRepositoryStub = $this->getMockBuilder(Repository::class)->getMock();

        $this->abstractServiceStub = $this
            ->getMockForAbstractClass(AbstractService::class, [
                $redisManagerStub,
                $redisSchemaDictionaryStub,
                $configRepositoryStub
            ]);
    }

    /**
     * @author PB
     */
    protected function tearDown()
    {
        $this->abstractServiceStub = null;
    }

    /**
     * @author PB
     */
    public function testGetSequenceLength()
    {
        $this->assertEquals(7, $this->abstractServiceStub->getSequenceLength());
    }

    /**
     * @author PB
     */
    public function testGetLastNumbersFromSequence()
    {
        $this->assertEquals([0,1,1,2,3,5,8], $this->abstractServiceStub->getLastNumbersFromSequence(7));
    }
}
