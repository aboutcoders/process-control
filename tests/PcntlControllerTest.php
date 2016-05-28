<?php
/*
* This file is part of the process-control package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\ProcessControl\Tests;


use Abc\ProcessControl\ControllerInterface;
use Abc\ProcessControl\NullController;
use Abc\ProcessControl\PcntlController;
use phpmock\phpunit\PHPMock;


class PcntlControllerTest extends \PHPUnit_Framework_TestCase
{
    use PHPMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $function_exists;


    public function setUp()
    {
        $this->function_exists    = $this->getFunctionMock('Abc\ProcessControl', 'function_exists');
    }

    /**
     * @param bool $doExit
     * @dataProvider  provideBooleanValues()
     */
    public function testWithFallbackController($doExit)
    {
        $this->function_exists->expects($this->any())
            ->willReturn(false);

        $fallbackController = $this->getMock(ControllerInterface::class);
        $fallbackController->expects($this->any())
            ->method('doExit')
            ->willReturn($doExit);

        $subject = new PcntlController(['SIGTERM'], $fallbackController);

        $this->assertEquals($doExit, $subject->doExit());
    }

    /**
     * @return array
     */
    public static function provideBooleanValues()
    {
        return [
            [true],
            [false]
        ];
    }
}
