<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen;

use ArrayAccess;
use IteratorAggregate;

/**
 * Session storage.
 *
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class Session implements ArrayAccess, IteratorAggregate
{
    public function __construct()
    {
        session_start();
    }

    /**
     * Is the key in the session.
     *
     * @param mixed $offset Offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($_SESSION[$offset]);
    }

    /**
     * Get session value.
     *
     * @param mixed $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($_SESSION[$offset]) ? $_SESSION[$offset] : null;
    }

    /**
     * Set session value.
     *
     * @param mixed $offset Offset
     * @param mixed $value  Value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $_SESSION[] = $value;
        } else {
            $_SESSION[$offset] = $value;
        }
    }

    /**
     * Unset session value.
     *
     * @param mixed $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($_SESSION[$offset]);
    }

    /**
     * Implements foreach.
     *
     * @return array
     */
    public function getIterator()
    {
        return $_SESSION;
    }

    /**
     * Regenerate session ID. Prevent session fixation attacks.
     *
     * @return void
     */
    public function regenerate()
    {
        session_regenerate_id();
    }

    /**
     * Destroy this session.
     *
     * @return void
     */
    public function destroy()
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }
}
