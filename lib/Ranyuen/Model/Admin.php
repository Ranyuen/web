<?php
/**
 * Ranyuen web site.
 */

namespace Ranyuen\Model;

use Illuminate\Database\Eloquent;

/**
 * Admin model.
 */
class Admin extends Eloquent\Model
{
    /**
     * Verisy the username and password.
     *
     * @param string $username    User name.
     * @param string $rawPassword Password.
     *
     * @return boolean
     */
    public static function isAuth($username, $rawPassword)
    {
        if (!($admin = self::where('username', $username)->first())) {
            return false;
        }

        return $admin->isPasswordCorrect($rawPassword);
    }

    protected $table = 'admin';

    /**
     * Hash the given password and set.
     *
     * @param string $rawPassword Password.
     */
    public function setPassword($rawPassword)
    {
        $this->password = password_hash($rawPassword, PASSWORD_DEFAULT);
    }

    /**
     * Varify the given passwoed.
     *
     * @param string $rawPassword Password.
     *
     * @return boolean
     */
    public function isPasswordCorrect($rawPassword)
    {
        return password_verify($rawPassword, $this->password);
    }
}
