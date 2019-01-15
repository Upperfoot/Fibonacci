<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Fibonacci;

use App\Service\Fibonacci\Formatter;
use PHPUnit\Framework\TestCase;

/**
 * Class FormatterTest
 * @author PB
 */
class FormatterTest extends TestCase
{
    /**
     * @var Formatter
     */
    protected $fibonacciFormatter;

    /**
     * @author PB
     */
    protected function setUp()
    {
        $this->fibonacciFormatter = new Formatter();
    }

    /**
     * @author PB
     */
    protected function tearDown()
    {
        $this->fibonacciFormatter = null;
    }

    /**
     * @author PB
     */
    public function testFormatArray()
    {
        $input = [
            '121393','514229','433494437',
            '317811','1346269','1836311903',
            '1888888125862690588922867465888183625889227465888183631190388880946'
        ];
        $formattedArray = $this->fibonacciFormatter->formatArray($input);

        $this->assertIsArray($formattedArray);
        $this->assertSame('121393', $formattedArray[0][1]);
        $this->assertSame('317811', $formattedArray[3][1]);
        $this->assertSame('INTEGER (67)', $formattedArray[6][1]);
    }

    /**
     * @author PB
     */
    public function testSortArrayAscending()
    {
        $ascendingSortedArray = $this->fibonacciFormatter->sortArrayAscending($this->arrayDataProvider());

        $this->assertIsArray($ascendingSortedArray);
        $this->assertSame([
            3 => '',
            11 => '',
            17 => '',
            18 => '',
            19 => '',
            20 => '',
            22 => '',
            57 => '',
            85 => '',
            90595 => ''
        ], $ascendingSortedArray);
    }

    /**
     * @author PB
     */
    public function testSortArrayDescending()
    {
        $descendingSortedArray = $this->fibonacciFormatter->sortArrayDescending($this->arrayDataProvider());

        $this->assertIsArray($descendingSortedArray);
        $this->assertSame([
            90595 => '',
            85 => '',
            57 => '',
            22 => '',
            20 => '',
            19 => '',
            18 => '',
            17 => '',
            11 => '',
            3 => ''
        ], $descendingSortedArray);
    }

    public function arrayDataProvider()
    {
        return [
            22 => '' ,
            18 => '' ,
            17 => '' ,
            57 => '' ,
            19 => '' ,
            20 => '' ,
            11 => '' ,
            85 => '' ,
            90595 => '' ,
            3 => ''
        ];
    }
}
