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
    public function readByte();
    public function readLine();

    public function writeByte();
    public function writeLine();

    public function close();

//    public function setBaudRate();
//    public function setParity();
//    public function setCharacterLength();
//    public function setStopBits();
//    public function setFlowControl();
}
