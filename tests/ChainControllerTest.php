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

use Abc\ProcessControl\ChainController;
use Abc\ProcessControl\ControllerInterface;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class ChainControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testDoExitIteratesOverAllControllers()
    {
        $controller1 = $this->getMockBuilder(ControllerInterface::class)->getMock();
        $controller2 = $this->getMockBuilder(ControllerInterface::class)->getMock();

        $controller1->expects($this->once())
            ->method('doStop')
            ->willReturn(false);

        $controller2->expects($this->once())
            ->method('doStop')
            ->willReturn(false);

        $subject = new ChainController([$controller1, $controller2]);

        $this->assertFalse($subject->doExit());
    }

    public function testDoExitReturnsTrue()
    {

        $controller1 = $this->getMockBuilder(ControllerInterface::class)->getMock();
        $controller2 = $this->getMockBuilder(ControllerInterface::class)->getMock();

        $controller1->expects($this->once())
            ->method('doStop')
            ->willReturn(false);

        $controller2->expects($this->once())
            ->method('doStop')
            ->willReturn(true);

        $subject = new ChainController([$controller1, $controller2]);

        $this->assertTrue($subject->doExit());
    }

    public function testDoExitReturnsTrueOnFirstController()
    {

        $controller1 = $this->getMockBuilder(ControllerInterface::class)->getMock();
        $controller2 = $this->getMockBuilder(ControllerInterface::class)->getMock();

        $controller1->expects($this->once())
            ->method('doStop')
            ->willReturn(true);

        $controller2->expects($this->never())
            ->method('doStop');

        $subject = new ChainController([$controller1, $controller2]);

        $this->assertTrue($subject->doExit());
    }

    public function testDoStopIteratesOverAllControllers()
    {
        $controller1 = $this->getMockBuilder(ControllerInterface::class)->getMock();
        $controller2 = $this->getMockBuilder(ControllerInterface::class)->getMock();

        $controller1->expects($this->once())
            ->method('doStop')
            ->willReturn(false);

        $controller2->expects($this->once())
            ->method('doStop')
            ->willReturn(false);

        $subject = new ChainController([$controller1, $controller2]);

        $this->assertFalse($subject->doStop());
    }

    public function testDoStopReturnsTrue()
    {

        $controller1 = $this->getMockBuilder(ControllerInterface::class)->getMock();
        $controller2 = $this->getMockBuilder(ControllerInterface::class)->getMock();

        $controller1->expects($this->once())
            ->method('doStop')
            ->willReturn(false);

        $controller2->expects($this->once())
            ->method('doStop')
            ->willReturn(true);

        $subject = new ChainController([$controller1, $controller2]);

        $this->assertTrue($subject->doStop());
    }

    public function testDoStopReturnsTrueOnFirstController()
    {
        $controller1 = $this->getMockBuilder(ControllerInterface::class)->getMock();
        $controller2 = $this->getMockBuilder(ControllerInterface::class)->getMock();

        $controller1->expects($this->once())
            ->method('doStop')
            ->willReturn(true);

        $controller2->expects($this->never())
            ->method('doStop');

        $subject = new ChainController([$controller1, $controller2]);

        $this->assertTrue($subject->doStop());
    }

    public function testDoPauseIteratesOverAllControllers()
    {
        $controller1 = $this->getMockBuilder(ControllerInterface::class)->getMock();
        $controller2 = $this->getMockBuilder(ControllerInterface::class)->getMock();

        $controller1->expects($this->once())
            ->method('doPause')
            ->willReturn(false);

        $controller2->expects($this->once())
            ->method('doPause')
            ->willReturn(false);

        $subject = new ChainController([$controller1, $controller2]);

        $this->assertFalse($subject->doPause());
    }

    public function testDoPauseReturnsTrue()
    {
        $controller1 = $this->getMockBuilder(ControllerInterface::class)->getMock();
        $controller2 = $this->getMockBuilder(ControllerInterface::class)->getMock();

        $controller1->expects($this->once())
            ->method('doPause')
            ->willReturn(false);

        $controller2->expects($this->once())
            ->method('doPause')
            ->willReturn(true);

        $subject = new ChainController([$controller1, $controller2]);

        $this->assertTrue($subject->doPause());
    }

    public function testDoPauseReturnsTrueOnFirstController()
    {
        $controller1 = $this->getMockBuilder(ControllerInterface::class)->getMock();
        $controller2 = $this->getMockBuilder(ControllerInterface::class)->getMock();

        $controller1->expects($this->once())
            ->method('doPause')
            ->willReturn(true);

        $controller2->expects($this->never())
            ->method('doPause');

        $subject = new ChainController([$controller1, $controller2]);

        $this->assertTrue($subject->doPause());
    }
}