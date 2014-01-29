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

/**
 * AbstractConnection
 *
 * @package Serially
 */
abstract class AbstractConnection
{
    /**
     * Parity settings
     */
    const PARITY_NONE = 0;
    const PARITY_ODD = 1;
    const PARITY_EVEN = 2;

    /**
     * Flow control settings
     */
    const FLOW_CONTROL_NONE = 0;
    const FLOW_CONTROL_RTS_CTS = 1;
    const FLOW_CONTROL_XON_XOFF = 2;


    protected $handle = null;
    protected $device = '';
    protected $newLine = PHP_EOL;
    protected $logger = null;

    public function __construct($device)
    {
        $this->logger = new NullLogger();
        $this->device = $device;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function execute($command)
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

        $this->logger->debug('  - stdout: ' . ($stdOut ?: '(empty)'));
        $this->logger->debug('  - stderr: ' . ($stdErr ?: '(empty)'));

        // Close pipes
        fclose($pipes[1]);
        fclose($pipes[2]);

        $result = proc_close($process);
        $this->logger->debug('  - exited: ' . $result);

        return $result;
    }
}
