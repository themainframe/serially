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
 * Implements serial communication on the Darwin/BSD (Mac OS X) platform.
 *
 * @package Serially
 */
class LinuxConnection extends AbstractConnection implements ConnectionInterface
{
    /**
     * @todo Implement me
     */
}
