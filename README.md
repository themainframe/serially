# Serially


Talk to serial devices in PHP from various* platforms.


## Getting It

Get [Composer](https://getcomposer.org/). Make your project require Serially.

    composer require themainframe/serially dev-master


## Examples

The `ConnectionManager` class enables you to write platform-portable code against Serially by abstracting the platform detection process away from your code.

The `getConnection` method returns an connection instance implementing `ConnectionInterface` suitable for the current platform.


    $manager = new ConnectionManager;
    $connection = $manager->getConnection('/dev/ttyS0');
    $connection->writeLine('Hello from PHP');
    
    
## Logging
    
Debugging serial devices and the communication between them can be difficult. Serially logs to a [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)-conformant `LoggerInterface` to make the process easier.

    // $myLogger implements LoggerInterface
    // Connections created with this manager will be logged to $myLogger
    $manager->setLogger($myLogger);
    
    
## Limitations

**Reading** bytes from serial ports is a challenge in PHP. Depending on the platform, you may observe
that data received before `readLine()` or `readByte()` is called may or may not be available.

**Platform** agility is a work-in-progress. The `PlatformSpecific` namespace contains platform-specific connection implementations (of `ConnectionInterface`) for Mac OS X (Darwin) and Linux. A Windows one may emerge soon. 

## Attributions &amp; Thanks

* Heavilly inspired by rubberneck's [php-serial](https://github.com/rubberneck/php-serial/).