<?php
/**
 * Serially
 * Talk to serial devices in PHP.
 *
 * @author Damien Walsh <me@damow.net>
 */

namespace Serially;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Serially\PlatformSpecific\LinuxConnection;
use Serially\PlatformSpecific\DarwinConnection;

/**
 * ConnectionManager
 *
 * Builds serial connections for a variety of platforms.
 *
 * @package Serially
 */
class ConnectionManager
{
    /**
     * Connections managed by this ConnectionManager
     *
     * @var array
     */
    private $connections = array();

    /**
     * A logger.
     *
     * @var LoggerInterface
     */
    private $logger = null;

    /**
     * Create a new Connection Manager.
     */
    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    /**
     * Connect to a device described by $deviceDescriptor, returning a
     * connection implementing the ConnectionInterface interface.
     *
     * @param string $deviceDescriptor The descriptor/path of the serial device
     * @throws \Exception
     * @return ConnectionInterface
     */
    public function getConnection($deviceDescriptor)
    {
        $platform = $this->detectPlatform();
        $this->logger->info('Platform identified as ' . $platform);

        switch ($platform) {

            case 'linux':

                $newConnection = new LinuxConnection($deviceDescriptor);
                $newConnection->setLogger($this->logger);
                $this->connections[] = $newConnection;

                return $newConnection;

                break;

            case 'darwin':

                $newConnection = new DarwinConnection($deviceDescriptor);
                $newConnection->setLogger($this->logger);
                $this->connections[] = $newConnection;

                return $newConnection;

                break;

            default:
                throw new \Exception('Unknown/unsupported platform.');
        }
    }

    /**
     * Try to detect the current platform.
     *
     * @throws \Exception
     * @return string
     */
    public function detectPlatform()
    {
        $uname = php_uname();

        if ('Linux' === substr($uname, 0, 5)) {

            // Ensure stty (set TTY) is available
            if (!$this->detectStty()) {
                throw new \Exception('stty is not available on this platform.');
            }

            return 'linux';

        } elseif ('Darwin' === substr($uname, 0, 6)) {
            // stty is available on Darwin/OS X
            return 'darwin';
        }

        // TODO: Add support for Windows

        // Platform detection failed
        return false;
    }

    /**
     * Set the logger that should be used by the manager and any connections
     * that it creates.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    private function detectStty()
    {
        return $this->execute('stty --version') === 0;
    }

    private function execute($command)
    {
        $desc = array(
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );

        // Execute the command
        $this->logger->debug('Execute: ' . $command);
        $process = proc_open($command, $desc, $pipes);

        // Get stdout and stderr
        $stdOut = stream_get_contents($pipes[1]);
        $stdErr = stream_get_contents($pipes[2]);

        // Close pipes
        fclose($pipes[1]);
        fclose($pipes[2]);

        return proc_close($process);
    }
}
