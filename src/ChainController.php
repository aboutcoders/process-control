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
     * @var ControllerInterface[]
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
        @trigger_error(sprintf('The %s method is deprecated since version 1.3.0 and will be removed in version 2.0. Use doStop() instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->doStop();
    }

    /**
     * {@inheritdoc}
     */
    public function doStop()
    {
        foreach ($this->controller as $controller) {

            if($controller->doStop())
            {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function doPause()
    {
        foreach ($this->controller as $controller) {

            if($controller->doPause())
            {
                return true;
            }
        }

        return false;
    }
}