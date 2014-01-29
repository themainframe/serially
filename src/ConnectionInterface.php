<?php
/**
 * Serially
 * Talk to serial devices in PHP.
 *
 * @author Damien Walsh <me@damow.net>
 */

namespace Serially;

/**
 * Interface ConnectionInterface
 *
 * Represents a platform-independent set of methods for reading and writing
 * data from a serial port.
 *
 * @package Serially
 */
interface ConnectionInterface
{
    /**
     * Open the connection.
     *
     * @param string $mode The fopen mode to use. Default rb+.
     * @return mixed
     */
    public function open($mode = 'rb+');

    /**
     * Read a single byte from the connection.
     *
     * @return mixed
     */
    public function readByte();

    /**
     * Read bytes from the connection until the newLine character appears.
     * Return the entire string.
     *
     * @return mixed
     */
    public function readLine();

    /**
     * Write a single character to the connection.
     *
     * If a string of multiple characters is passed, only the first character will
     * be written.
     *
     * @param string $byte
     * @return mixed
     */
    public function writeByte($byte);

    /**
     * Write $line to the connection, followed by the defined newLine character.
     *
     * @param string $line The line to write.
     * @return mixed
     */
    public function writeLine($line);

    /**
     * Close the connection.
     */
    public function close();

    /**
     * Set the baud rate of the connection.
     *
     * @param int $rate The baud rate as a constant from AbstractConnection
     * @return mixed
     */
    public function setBaudRate($rate);

    /**
     * Set the parity used by the connection.
     *
     * @param int $parity The parity as a constant from AbstractConnection
     * @return mixed
     */
    public function setParity($parity);

    /**
     * Set the number of data bits used for the connection.
     *
     * @param int $bits The number of data bits to use.
     * @return mixed
     */
    public function setDataBits($bits);

    /**
     * Set the number of stop bits used for the connection.
     *
     * @param int $bits The number of stop bits to use.
     * @return mixed
     */
    public function setStopBits($bits);

    /**
     * Set the flow control mode used for the connection.
     *
     * @param int $mode The flow control mode as a constant from AbstractConnection
     * @return mixed
     */
    public function setFlowControl($mode);
}
