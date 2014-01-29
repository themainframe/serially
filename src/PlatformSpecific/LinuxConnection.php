<?php
/**
 * Serially
 * Talk to serial devices in PHP.
 *
 * @author Damien Walsh <me@damow.net>
 */

namespace Serially\PlatformSpecific;

use Serially\AbstractConnection;
use Serially\ConnectionInterface;

/**
 * LinuxConnection
 *
 * Implements serial communication on a Linux platform.
 *
 * @package Serially
 */
class LinuxConnection extends AbstractConnection implements ConnectionInterface
{
    /**
     * Open the connection.
     *
     * @param string $mode The fopen mode to use. Default rb+.
     * @throws \Exception
     * @return mixed
     */
    public function open($mode = 'rb+')
    {
        $this->logger->info('Opening connection on ' . $this->device);
        $this->execute('stty -F ' . $this->device);
        $this->execute('stty -F ' . $this->device . ' -isig -icanon -brkint -icrnl -imaxbel');
        $this->handle = @fopen($this->device, $mode);

        if (!$this->handle) {
            throw new \Exception('fopen failed for device: ' . $this->device);
        }

        stream_set_blocking($this->handle, 1);
        return $this->handle;
    }

    /**
     * Close the connection.
     */
    public function close()
    {
        if ($this->handle) {
            @fclose($this->handle);
        }
    }

    /**
     * Read a single byte from the connection.
     *
     * @return mixed
     */
    public function readByte()
    {
        return fread($this->handle, 1);
    }

    /**
     * Read bytes from the connection until the newLine character appears.
     * Return the entire string.
     *
     * @return mixed
     */
    public function readLine()
    {
        $content = '';
        $c = '';

        while (!feof($this->handle) && $c != $this->newLine) {
            $c = fread($this->handle, 1);
            $content .= $c;
        }

        return $content;
    }

    /**
     * Write a single character to the connection.
     *
     * If a string of multiple characters is passed, only the first character will
     * be written.
     *
     * @param string $byte
     * @return mixed
     */
    public function writeByte($byte)
    {
        fwrite($this->handle, substr($byte, 0, 1));
    }

    /**
     * Write $line to the connection, followed by the defined newLine character.
     *
     * @param string $line The line to write.
     * @return mixed
     */
    public function writeLine($line)
    {
        fwrite($this->handle, $line . $this->newLine);
    }

    /**
     * Set the baud rate of the connection.
     *
     * @param int $rate The baud rate as a constant from AbstractConnection
     * @return mixed
     */
    public function setBaudRate($rate)
    {
        // For Linux, $rate is the literal baud rate to assign - no conversion
        $this->execute('stty -F ' . $this->device . ' ' . intval($rate));
    }

    /**
     * Set the parity used by the connection.
     *
     * @param int $parity The parity as a constant from AbstractConnection
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function setParity($parity)
    {
        // Define the Linux stty flags for various parity values
        $arguments = array(
            static::PARITY_NONE => '-parenb',
            static::PARITY_ODD => 'parenb parodd',
            static::PARITY_EVEN => 'parenb -parodd'
        );

        if (!array_key_exists($parity, $arguments)) {
            throw new \InvalidArgumentException(
                'Parity must be one of PARITY_NONE, PARITY_ODD or PARITY_EVEN.'
            );
        }

        $this->execute('stty -F ' . $this->device . ' ' . $arguments[$parity]);
    }

    /**
     * Set the number of data bits used for the connection.
     *
     * @param int $bits The number of data bits to use.
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function setDataBits($bits)
    {
        // Check the requested number of data bits is valid
        if ($bits < 5 || $bits > 8) {
            throw new \InvalidArgumentException(
                'There cannot be more than 8 or fewer than 5 data bits.'
            );
        }

        // Assign the value with stty
        $this->execute('stty -F ' . $this->device . ' cs' . intval($bits));
    }

    /**
     * Set the number of stop bits used for the connection.
     *
     * @param int $bits The number of stop bits to use.
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function setStopBits($bits)
    {
        // Define the allowed numbers of stop bits for this platform
        $allowedSizes = array(
            1, 2, 1.5
        );

        if (!in_array($bits, $allowedSizes)) {
            throw new \InvalidArgumentException(
                'Only these stop bits are allowed for this platform: ' .
                implode(',', $allowedSizes)
            );
        }

        // Assign the value with stty
        $this->execute(
            'stty -F ' . $this->device . ' ' .
            ($bits == 1 ? '-' : '') . 'cstopb'
        );
    }

    /**
     * Set the flow control mode used for the connection.
     *
     * @param int $mode The flow control mode as a constant from AbstractConnection
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function setFlowControl($mode)
    {
        // Define the Linux stty flags for various flow control types
        $arguments = array(
            static::FLOW_CONTROL_NONE => 'clocal -crtscts -ixon -ixoff',
            static::FLOW_CONTROL_RTS_CTS => '-clocal crtscts -ixon -ixoff',
            static::FLOW_CONTROL_XON_XOFF => '-clocal -crtscts ixon ixoff'
        );

        if (!array_key_exists($mode, $arguments)) {
            throw new \InvalidArgumentException(
                'Flow control mode must be one of FLOW_CONTROL_NONE, FLOW_CONTROL_RTS_CTS' .
                ' or FLOW_CONTROL_XON_XOFF.'
            );
        }

        $this->execute('stty -F ' . $this->device . ' ' . $arguments[$mode]);
    }
}
