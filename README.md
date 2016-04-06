Process Control
===============

A PHP process control library.

## The interface

The interface Controller defines the method doExit() that determines whether to exist a process.

```php
interface Controller
{
    /**
     * Indicates whether to exit a process
     *
     * @return boolean
     */
    public function doExit();
}
```

## The PCNTL controller

The is one implementation of the interface that listens to PCNTL events in order to determine whether to exit a process.

```php
    $stopsignals = array(SIGTERM);
    $logger = new Psr\Log\NullLogger();
    
    $controller = new PcntlController($stopsignals, $logger);
    
    while(!$controller->doExit())
    {
        // do something
    }
```