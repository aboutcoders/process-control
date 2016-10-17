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
use Abc\ProcessControl\PcntlController;
use phpmock\phpunit\PHPMock;

/**
 * @runTestsInSeparateProcesses
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class PcntlControllerTest extends \PHPUnit_Framework_TestCase
{
    use PHPMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $extension_loaded;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->extension_loaded = $this->getFunctionMock('Abc\ProcessControl', 'extension_loaded');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testConstructWithPcntlExtensionNotLoaded()
    {
        $this->extension_loaded->expects($this->any())
            ->willReturn(false);

        new PcntlController(['SIGTERM']);
    }

    /**
     * @dataProvider provideInvalidSignals
     * @expectedException \InvalidArgumentException
     * @param array $stopSignals
     * @param array $pauseSignals
     * @param array $resumeSignals
     */
    public function testConstructWithInvalidSignals(array $stopSignals = array(), array $pauseSignals = array(), array $resumeSignals = array())
    {
        $this->extension_loaded->expects($this->any())
            ->willReturn(true);

        new PcntlController($stopSignals, $pauseSignals, $resumeSignals);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testConstructWithSignalHandlerRegistrationFails()
    {
        $this->extension_loaded->expects($this->any())
            ->willReturn(true);

        $pcntl_signal = $this->getFunctionMock('Abc\ProcessControl', 'pcntl_signal');

        $pcntl_signal->expects($this->any())
            ->willReturn(false);

        new PcntlController(['SIGTERM']);
    }

    /**
     * @param bool $value
     * @dataProvider  provideBooleanValues()
     */
    public function testDoExit($value)
    {
        /**
         * @var PcntlController|\PHPUnit_Framework_MockObject_MockObject $subject
         */
        $subject = $this->getMockBuilder(PcntlController::class)
            ->disableOriginalConstructor()
            ->setMethods(['doStop'])
            ->getMock();

        $subject->expects($this->once())
            ->method('doStop')
            ->willReturn($value);

        $this->assertEquals($value, $subject->doExit());
    }

    public function testDoStop()
    {
        $this->extension_loaded->expects($this->any())
            ->willReturn(true);

        $subject = new PcntlController([SIGTERM]);

        $this->assertFalse($subject->doStop());
    }

    /**
     * @param bool $value
     * @dataProvider  provideBooleanValues()
     */
    public function testDoStopWithFallbackController($value)
    {
        $this->extension_loaded->expects($this->any())
            ->willReturn(false);

        $fallbackController = $this->getMockBuilder(ControllerInterface::class)->getMock();
        $fallbackController->expects($this->any())
            ->method('doStop')
            ->willReturn($value);

        $subject = new PcntlController(['SIGTERM'], [], [], $fallbackController);

        $this->assertEquals($value, $subject->doStop());
    }

    public function testDoPause()
    {
        $this->extension_loaded->expects($this->any())
            ->willReturn(true);

        $subject = new PcntlController([SIGTERM]);

        $this->assertFalse($subject->doPause());
    }

    /**
     * @param bool $value
     * @dataProvider  provideBooleanValues()
     */
    public function testDoPauseWithFallbackController($value)
    {
        $this->extension_loaded->expects($this->any())
            ->willReturn(false);

        $fallbackController = $this->getMockBuilder(ControllerInterface::class)->getMock();
        $fallbackController->expects($this->any())
            ->method('doPause')
            ->willReturn($value);

        $subject = new PcntlController(['SIGTERM'], [], [], $fallbackController);

        $this->assertEquals($value, $subject->doPause());
    }

    /**
     * @dataProvider provideSignals
     * @param null $signal
     */
    public function testHandleSignal($signal)
    {
        $stopSignals   = [SIGTERM];
        $pauseSignals  = [SIGUSR2];
        $resumeSignals = [SIGCONT];

        $this->extension_loaded->expects($this->any())
            ->willReturn(true);

        $subject = new PcntlController($stopSignals, $pauseSignals, $resumeSignals);

        $class  = new \ReflectionClass($subject);
        $method = $class->getMethod('handleSignal');
        $method->setAccessible(true);

        $method->invokeArgs($subject, [$signal]);

        if (in_array($signal, $stopSignals)) {
            $this->assertTrue($subject->doStop());
            $this->assertTrue($subject->doExit());
        } elseif (in_array($signal, $pauseSignals)) {
            $this->assertTrue($subject->doPause());
        } elseif (in_array($signal, $resumeSignals)) {
            $this->assertFalse($subject->doPause());
        }
    }

    /**
     * @return array
     */
    public static function provideInvalidSignals()
    {
        return [
            [['INVALID']],
            [[], ['INVALID']],
            [[], [], ['INVALID']],
            [[-1]],
            [[], [-1]],
            [[], [], [-1]],
        ];
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

    public static function provideSignals()
    {
        return [
            [SIGTERM],
            [SIGUSR2],
            [SIGCONT]
        ];
    }
}