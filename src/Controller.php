<?php

namespace Abc\ProcessControl;

/**
 * Provides information whether execution of a process should be stopped.
 *
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
interface Controller
{
    /**
     * Indicates whether to exit a process.
     *
     * @return boolean
     */
    public function doExit();
}