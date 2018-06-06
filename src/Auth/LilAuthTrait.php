<?php

namespace Lil\Auth;

use Cake\Core\Configure;

trait LilAuthTrait
{
    private $_Auth = false;

    protected function setAuth($Auth)
    {
        $this->_Auth = $Auth;
    }

    /**
     * Checks if user has specified role
     *
     * @param string|int $role User role.
     * @param array|object|null $user User data.
     * @return bool
     */
    public function userLevel($role, $user = null)
    {
        if (!empty($user)) {
            if (is_array($user)) {
                $userPrivilege = $user[Configure::read('Lil.userLevelField')];
            } elseif (is_object($user)) {
                $userPrivilege = $user->{Configure::read('Lil.userLevelField')};
            }
        } else {
            if (!$this->_Auth) {
                return false;
            }
            $userPrivilege = $this->_Auth->user(Configure::read('Lil.userLevelField'));
        }

        if (is_string($role)) {
            switch ($role) {
                case 'root':
                    return $userPrivilege <= Configure::read('Lil.userLevelRoot');
                case 'admin':
                    return $userPrivilege <= Configure::read('Lil.userLevelAdmin');
                default:
                    return true;
            }
        } else {
            return $userPrivilege <= $role;
        }
    }
}
