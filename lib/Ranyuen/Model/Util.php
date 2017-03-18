<?php

namespace Ranyuen\Model;

use Illuminate\Database\Eloquent;
use Ramsey\Uuid\Uuid;

/**
 * Util Model
 */
class Util extends Eloquent\Model
{
    /**
     * makeUuid
     *
     * @return String uuid
     */
    public static function makeUuid()
    {
        try {
            $uuid = Uuid::uuid4();
            return $uuid;
        } catch(UnsatisfiedDependencyException $e) {
        }
    }

    /**
     * FileExtensionGetAllowUpload
     *
     * @param [type] $ext [description]
     *
     * @return boolean
     */
    public static function FileExtensionGetAllowUpload($ext){
        $allowExt = array('jpg');
        foreach ($allowExt as $v) {
          if ($v === $ext) {
            return true;
          }
        }
        return false;
    }
}
