<?php
declare(strict_types=1);

namespace Lil\Auth;

use Cake\Core\Configure;

trait LilAuthTrait
{
    private mixed $_Auth = false;

    /**
     * Set Auth component instance
     *
     * @param mixed $Auth Auth component.
     * @return void
     */
    protected function setAuth(mixed $Auth): void
    {
        $this->_Auth = $Auth;
    }

    /**
     * Checks if user has specified role
     *
     * @param string|int $role User role.
     * @param object|array|null $user User data.
     * @return bool
     */
    public function userLevel(string|int $role, array|object|null $user = null): bool
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
