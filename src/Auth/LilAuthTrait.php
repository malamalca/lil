<?php

namespace Lil\Auth;

use Cake\Core\Configure;

trait LilAuthTrait
{
    private $_Auth = [];

    protected function setAuth($Auth)
    {
        $this->_Auth = $Auth;
    }

    /**
     * Checks if user has specified role
     *
     * @param string|int $role User role.
     * @return boolean
     */
    public function userLevel($role)
    {
        if (is_string($role)) {
            switch ($role) {
                case 'root':
                    return $this->_Auth->user(Configure::read('Lil.userLevelField')) <= Configure::read('Lil.userLevelRoot');
                case 'admin':
                    return $this->_Auth->user(Configure::read('Lil.userLevelField')) <= Configure::read('Lil.userLevelAdmin');
                default:
                    return true;
            }
        } else {
            return $this->_Auth->user('privileges') <= $role;
        }
    }   
}