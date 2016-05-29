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
     * @var bool
     */
    private $stop = false;

    /**
     * @param array                $stopSignals        The signal values or the names of the signals that stop the iteration
     * @param ControllerInterface  $fallbackController A fallback controller that will be used if PCNTL functions do not exist (e.g if not run in PHP CLI mode)
     * @param LoggerInterface|null $logger
     * @throws \RuntimeException If registration of the handler for a certain signal fails
     * @throws \InvalidArgumentException If one of the stop signals is invalid
     */
    public function __construct(array $stopSignals, ControllerInterface $fallbackController = null, LoggerInterface $logger = null)
    {
        $this->logger = $logger == null ? new NullLogger() : $logger;

        $this->logger->info('Initialize PCNTL process controller for signals {signals}', array('signals' => $stopSignals));

        if ($fallbackController != null && (!function_exists('pcntl_signal') || !function_exists('pcntl_signal_dispatch'))) {
            $this->fallbackController = $fallbackController;
            $this->logger->warning('PcntlController switched to fallback controller because PCNTL functions do not exist');
        } else {

            if (!function_exists('pcntl_signal')) {
                throw new \RuntimeException('The function "pcntl_signal" does not exist. (see http://de2.php.net/manual/en/book.pcntl.php)');
            }
            if (!function_exists('pcntl_signal_dispatch')) {
                throw new \RuntimeException('The function "pcntl_signal_dispatch" does not exist. (see http://de2.php.net/manual/en/book.pcntl.php)');
            }

            foreach ($stopSignals as $value) {
                if (is_string($value)) {
                    $value = @constant($value);
                    if (null === $value) {
                        $this->logger->error(sprintf('The value "%s" does not specify name of a PCNTL signal constant', $value));

                        throw new \InvalidArgumentException(sprintf('The value "%s" does not specify name of a PCNTL signal constant', $value));
                    }
                }

                if (!is_int($value) || $value <= 0) {
                    $this->logger->error(sprintf('The value "%s" is not a valid process signal value', $value));

                    throw new \InvalidArgumentException(sprintf('The value "%s" is not a valid process signal value', $value));
                }

                if (false === @pcntl_signal($value, array($this, 'handleSignal'))) {
                    $this->logger->error(sprintf('Failed to register handler for signal "%s"', $value));

                    throw new \RuntimeException(sprintf('Failed to register handler for signal "%s"', $value));
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function doExit()
    {
        if ($this->fallbackController != null) {
            return $this->fallbackController->doExit();
        }

        pcntl_signal_dispatch();

        if ($this->stop == true) {
            $this->logger->info('Inform process to exit gracefully');
        }

        return $this->stop;
    }

    /**
     * Internal callback function registered with pcntl_signal()
     *
     * @param int $signal
     */
    private function handleSignal($signal)
    {
        $this->logger->info(sprintf('Handle PCNTL signal %s', $signal));

        $this->stop = true;
    }
}