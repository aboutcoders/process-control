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
class PcntlController implements Controller
{
    /** @var bool */
    private $stop = false;
    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param array                $stopSignals The signal values or the names of the signals that stop the iteration
     * @param LoggerInterface|null $logger
     * @throws \InvalidArgumentException If $stopSignals contains an invalid signal value
     * @throws \Exception If the functions pcntl_signal or pcntl_signal_dispatch do not exist
     * @throws \Exception If registration of the handler for a certain signal fails
     */
    public function __construct(array $stopSignals, LoggerInterface $logger = null)
    {
        $this->logger = $logger == null ? new NullLogger() : $logger;

        $this->logger->info('Initialize PCNTL process controller for signals {signals}', array('signals' => $stopSignals));

        if(!function_exists('pcntl_signal'))
        {
            throw new \Exception('The function "pcntl_signal" does not exist. (see http://de2.php.net/manual/en/book.pcntl.php)');
        }
        if(!function_exists('pcntl_signal_dispatch'))
        {
            throw new \Exception('The function "pcntl_signal_dispatch" does not exist. (see http://de2.php.net/manual/en/book.pcntl.php)');
        }

        foreach($stopSignals as $value)
        {
            if(is_string($value))
            {
                $value = @constant($value);
                if(null === $value)
                {
                    $this->logger->error(sprintf('The value "%s" does not specify name of a PCNTL signal constant', $value));

                    throw new \InvalidArgumentException(sprintf('The value "%s" does not specify name of a PCNTL signal constant', $value));
                }
            }

            if(!is_int($value) || $value <= 0)
            {
                $this->logger->error(sprintf('The value "%s" is not a valid process signal value', $value));

                throw new \InvalidArgumentException(sprintf('The value "%s" is not a valid process signal value', $value));
            }

            if(false === @pcntl_signal($value, array($this, 'handleSignal')))
            {
                $this->logger->error(sprintf('Failed to register handler for signal "%s"', $value));

                throw new \Exception(sprintf('Failed to register handler for signal "%s"', $value));
            }
        }


    }

    /**
     * {@inheritdoc}
     */
    public function doExit()
    {
        pcntl_signal_dispatch();

        if($this->stop == true)
        {
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