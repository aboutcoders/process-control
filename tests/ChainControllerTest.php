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

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class ChainControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testDoExitIteratesOverAllControllers()
    {
        $controller1 = $this->getMock('Abc\ProcessControl\ControllerInterface');
        $controller2 = $this->getMock('Abc\ProcessControl\ControllerInterface');

        $controller1->expects($this->once())
            ->method('doExit')
            ->willReturn(false);

        $controller2->expects($this->once())
            ->method('doExit')
            ->willReturn(false);

        $subject = new ChainController([$controller1, $controller2]);

        $this->assertFalse($subject->doExit());
    }

    public function testDoExitReturnsTrue() {

        $controller1 = $this->getMock('Abc\ProcessControl\ControllerInterface');
        $controller2 = $this->getMock('Abc\ProcessControl\ControllerInterface');

        $controller1->expects($this->once())
            ->method('doExit')
            ->willReturn(false);

        $controller2->expects($this->once())
            ->method('doExit')
            ->willReturn(true);

        $subject = new ChainController([$controller1, $controller2]);

        $this->assertTrue($subject->doExit());
    }

    public function testDoExitReturnsTrueOnFirstController() {

        $controller1 = $this->getMock('Abc\ProcessControl\ControllerInterface');
        $controller2 = $this->getMock('Abc\ProcessControl\ControllerInterface');

        $controller1->expects($this->once())
            ->method('doExit')
            ->willReturn(true);

        $controller2->expects($this->never())
            ->method('doExit');

        $subject = new ChainController([$controller1, $controller2]);

        $this->assertTrue($subject->doExit());
    }
}