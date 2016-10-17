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

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * PcntlController determines whether to stop execution based on PCNTL events.
 *
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class PcntlController implements ControllerInterface
{
    /**
     * @var ControllerInterface
     */
    protected $fallbackController;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    private $stopSignals, $pauseSignals, $resumeSignals = array();

    /**
     * @var bool
     */
    private $stop = false;

    /**
     * @var bool
     */
    private $pause = false;

    /**
     * @param array                $stopSignals        An array of signal values or name of signals that indicate to stop processing
     * @param array                $pauseSignals       An array of signal values or name of signals that indicate to pause processing
     * @param array                $resumeSignals      An array of signal values or name of signals that indicate to resume processing
     * @param ControllerInterface  $fallbackController A fallback controller that will be used if PCNTL extension is not loaded
     * @param LoggerInterface|null $logger
     */
    public function __construct(array $stopSignals, array $pauseSignals = array(), array $resumeSignals = array(), ControllerInterface $fallbackController = null, LoggerInterface $logger = null)
    {
        $this->stopSignals        = $this->cleanSignals($stopSignals);
        $this->pauseSignals       = $this->cleanSignals($pauseSignals);
        $this->resumeSignals      = $this->cleanSignals($resumeSignals);
        $this->fallbackController = $fallbackController;
        $this->logger             = $logger == null ? new NullLogger() : $logger;

        $this->logger->info('Initialize PCNTL controller for signals {signals}', array('signals' => $stopSignals));

        if (!extension_loaded('pcntl') && $fallbackController == null) {
            throw new \RuntimeException('The PCNTL extension is not loaded');
        } elseif (!extension_loaded('pcntl') && $fallbackController != null) {

            $this->logger->warning('PcntlController switched to fallback controller because PCNTL functions do not exist');
        } else {
            foreach (array_merge($this->stopSignals, $this->pauseSignals, $this->resumeSignals) as $value) {
                if (false === @pcntl_signal($value, array(&$this, 'handleSignal'))) {
                    throw new \RuntimeException(sprintf('Failed to register handler for signal "%s"', $value));
                }
            }
        }
    }

    /**
     * @deprecated since 1.3.0 (to be removed in 2.0)
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
        if ($this->fallbackController != null) {
            return $this->fallbackController->doStop();
        }

        pcntl_signal_dispatch();

        return $this->stop;
    }

    /**
     * {@inheritdoc}
     */
    public function doPause()
    {
        if ($this->fallbackController != null) {
            return $this->fallbackController->doPause();
        }

        pcntl_signal_dispatch();

        return $this->pause;
    }

    /**
     * Internal callback function registered with pcntl_signal()
     *
     * @param int $signal
     */
    protected function handleSignal($signal)
    {
        if (in_array($signal, $this->stopSignals)) {
            $this->logger->info(sprintf('Handle stop signal (%s)', $signal));
            $this->stop = true;
        } elseif (in_array($signal, $this->pauseSignals)) {
            $this->logger->info(sprintf('Handle pause signal (%s)', $signal));
            $this->pause = true;
        } elseif (in_array($signal, $this->resumeSignals)) {
            $this->logger->info(sprintf('Handle resume signal (%s)', $signal));
            $this->pause = false;
        }
    }

    /**
     * Cleans a signal value.
     *
     * @param array $values
     * @return array The cleaned values
     * @throws \InvalidArgumentException If validation fails
     */
    private function cleanSignals(array $values)
    {
        $cleanedValues = array();
        foreach ($values as $value) {
            if (is_string($value)) {
                $value = @constant($value);
                if (null === $value) {
                    throw new \InvalidArgumentException(sprintf('The value "%s" does not specify name of a PCNTL signal constant', $value));
                }
            }

            if (!is_int($value) || $value <= 0) {
                throw new \InvalidArgumentException(sprintf('The value "%s" is not a valid process signal value', $value));
            }

            $cleanedValues[] = $value;
        }

        return $cleanedValues;
    }
}