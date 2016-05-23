<?php
/*
* This file is part of the process-control package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\ProcessControl;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class ChainController implements ControllerInterface
{
    /**
     * @var array ControllerInterface[]
     */
    private $controller;

    /**
     * ChainController constructor.
     *
     * @param array ControllerInterface[] $controller
     */
    public function __construct(array $controller)
    {
        $this->controller = $controller;
    }

    /**
     * {@inheritdoc}
     */
    public function doExit()
    {
        foreach ($this->controller as $controller) {

            if($controller->doExit())
            {
                return true;
            }
        }

        return false;
    }
}