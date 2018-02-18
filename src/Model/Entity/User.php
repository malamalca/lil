<?php
/**
 * User Entity
 *
 * @category Entity
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
namespace Lil\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @category Entity
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
class User extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true
    ];

    /**
     * Set password method.
     *
     * @param string $password Users password.
     * @return bool
     */
    protected function _setPasswd($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }

    /**
     * Checks if user has specified role
     *
     * @param int $role Users role.
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->privileges <= $role;
    }
}
