<?php
/**
 * Serially
 * Talk to serial devices in PHP.
 *
 * @author Damien Walsh <me@damow.net>
 */

namespace Serially;

/**
 * DarwinConnection
 *
 * Implements serial communication on the Darwin/BSD (Mac OS X) platform.
 *
 * @package Serially
 */
class DarwinConnection extends AbstractConnection implements ConnectionInterface
{
    public function open($mode = 'r+b')
    {
        $this->exec('stty -f ' . $this->device);
        $this->handle = @fopen($this->device, $mode);

        if (!$this->handle) {
            throw new \Exception('fopen failed for device: ' . $this->device);
        }

        stream_set_blocking($this->handle, 0);
        return $this->handle;
    }

    public function close()
    {
        if ($this->handle) {
            @fclose($this->handle);
        }
    }

    public function readByte()
    {
        return fread($this->handle, 1);
    }

    public function readLine()
    {
        $buffer = '';

        while (($c = fread($this->handle, 1)) !== $this->newLine) {
            $buffer .= $c;
        }

        return $buffer;
    }

    public function writeByte($byte)
    {
        fwrite($this->handle, substr($byte, 0, 1));
    }

    public function writeLine($line)
    {
        fwrite($this->handle, $line . $this->newLine);
    }
}
