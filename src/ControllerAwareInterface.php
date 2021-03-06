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
interface ControllerAwareInterface
{
    /**
     * @param ControllerInterface $controller
     * @return void
     */
    public function setController(ControllerInterface $controller);
}