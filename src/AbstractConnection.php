<?php
/**
 * Serially
 * Talk to serial devices in PHP.
 *
 * @author Damien Walsh <me@damow.net>
 */

namespace Serially;

/**
 * AbstractConnection
 *
 * @package Serially
 */
abstract class AbstractConnection
{
    protected $handle = null;
    protected $device = '';
    protected $newLine = PHP_EOL;

    public function __construct($device)
    {
        $this->device = $device;
    }

    protected function exec($command)
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
