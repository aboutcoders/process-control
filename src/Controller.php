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
 * Provides information whether execution of a process should be stopped.
 *
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
interface Controller
{
    /**
     * Indicates whether to exit a process
     *
     * @return boolean
     */
    public function doExit();
}