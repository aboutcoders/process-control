Process Control
===============

A PHP process control library.

Build Status: [![Build Status](https://travis-ci.org/aboutcoders/process-control.svg?branch=master)](https://travis-ci.org/aboutcoders/process-control)

## The interface

The [ControllerInterface](./src/ControllerInterface.php) defines the method doExit() that indicates whether to exist a process.

```php
interface ControllerInterface
{
    /**
     * Indicates whether to exit a process
     *
     * @return boolean
     */
    public function doExit();
}
```

## The PcntlController

The [PcntlController](./src/PcntlController.php) listens to PCNTL events to determine whether to exit a process.

```php
    $stopsignals = array(SIGTERM);
    $logger = new Psr\Log\NullLogger();
    
    $controller = new PcntlController($stopsignals, $logger);
    
    while(!$controller->doExit())
    {
        // do something
    }
```

## The ChainController

The [ChainController](./src/ChainController.php) executes multiple controllers in a chain to determine whether to exit a process.

## The NullController

The [NullController](./src/NullController.php) never indicates to exit a process.

__Note: This controller can be used as fallback controller for the PcntlController in runtime environments where PCNTL functions to not exist.__