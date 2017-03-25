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
     * fileExtensionGetAllowUpload
     *
     * @param [type] $ext [description]
     *
     * @return boolean
     */
    public static function fileExtensionGetAllowUpload($ext){
        $allowExt = array('jpg');
        foreach ($allowExt as $v) {
          if ($v === $ext) {
            return true;
          }
        }
        return false;
    }

    /**
     * uploadFileValidator description
     *
     * @param  [file] $uploadFiles [description]
     *
     * @return boolean
     */
    public static function uploadFileValidator($uploadFiles)
    {
        foreach ($uploadFiles['error'] as $error) {
            if (!isset($error) && !is_int($error)) {
                return false;
            }
        }
        return true;
    }
}
