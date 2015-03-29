<?php

/**
 * Ranyuen web site.
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
