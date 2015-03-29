<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen;

use Ranyuen\Little\Response;

/**
 * Immutable HTTP response.
 */
class FrozenResponse extends Response
{
    public function setStatusCode($code, $text = null)
    {
        if ($this->getStatusCode()) {
            return $this;
        }

        return parent::setStatusCode($code, $text);
    }
}
