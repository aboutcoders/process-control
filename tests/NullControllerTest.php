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

use Abc\ProcessControl\NullController;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class NullControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testDoExit()
    {
        $subject = new NullController;
        $this->assertFalse($subject->doExit());
    }

    public function testDoStop()
    {
        $subject = new NullController;
        $this->assertFalse($subject->doStop());
    }

    public function testDoPause()
    {
        $subject = new NullController;
        $this->assertFalse($subject->doPause());
    }
}