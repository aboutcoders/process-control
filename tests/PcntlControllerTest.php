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
     * @param bool $doExit
     * @dataProvider  provideBooleanValues()
     */
    public function testDoExistWithFallbackController($doExit)
    {
        $this->extension_loaded->expects($this->any())
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
