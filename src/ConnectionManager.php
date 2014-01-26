<?php
/**
 * Serially
 * Talk to serial devices in PHP.
 *
 * @author Damien Walsh <me@damow.net>
 */

namespace Serially;

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
     * Connect to a device described by $deviceDescriptor, returning a
     * connection implementing the ConnectionInterface interface.
     *
     * @param string $deviceDescriptor The descriptor/path of the serial device
     * @throws \Exception
     * @return ConnectionInterface
     */
    public function getConnection($deviceDescriptor)
    {
        switch ($this->detectPlatform()) {

            case 'linux':

                $newConnection = new LinuxConnection($deviceDescriptor);
                $this->connections[] = $newConnection;

                return $newConnection;

                break;

            case 'darwin':

                $newConnection = new DarwinConnection($deviceDescriptor);
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
        return '';
    }

    private function detectStty()
    {
        return $this->exec('stty --version') === 0;
    }

    private function exec($command)
    {
        $desc = array(
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );

        // Execute the command
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
