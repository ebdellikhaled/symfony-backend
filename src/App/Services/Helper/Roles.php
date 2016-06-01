<?php
/**
 * /src/App/Services/Helper/Roles.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace App\Services\Helper;

/**
 * Class Roles
 *
 * @see /app/config/services_helper.yml
 *
 * @category    Service
 * @package     App\Services\Helper
 * @author      TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class Roles
{
    const ROLE_LOGGED   = 'ROLE_LOGGED';
    const ROLE_USER     = 'ROLE_USER';
    const ROLE_ADMIN    = 'ROLE_ADMIN';
    const ROLE_ROOT     = 'ROLE_ROOT';

    /**
     * Roles hierarchy.
     *
     * @var array
     */
    private $rolesHierarchy;

    /**
     * RolesHelper constructor.
     *
     * @param   array   $rolesHierarchy This is a 'security.role_hierarchy.roles' parameter value
     *
     * @return  Roles
     */
    public function __construct(array $rolesHierarchy)
    {
        $this->rolesHierarchy = $rolesHierarchy;
    }

    /**
     * Getter method to return all roles in single dimensional array.
     *
     * @return string[]
     */
    public function getRoles()
    {
        $roles = [
            self::ROLE_LOGGED,
            self::ROLE_USER,
            self::ROLE_ADMIN,
            self::ROLE_ROOT,
        ];

        return $roles;
    }

    /**
     * Getter method for role label.
     *
     * @param   string  $role
     *
     * @return  string
     */
    public function getRoleLabel($role)
    {
        switch ($role) {
            case self::ROLE_LOGGED:
                $output = 'Logged in users';
                break;
            case self::ROLE_USER:
                $output = 'Normal users';
                break;
            case self::ROLE_ADMIN:
                $output = 'Admin users';
                break;
            case self::ROLE_ROOT:
                $output = 'Root users';
                break;
            default:
                $output = 'Unknown - ' . $role;
                break;
        }

        return $output;
    }

    /**
     * Getter method for short role.
     *
     * @param   string $role
     *
     * @return  string
     */
    public function getShort($role)
    {
        return mb_strtolower(mb_substr($role, (mb_strpos($role, '_') + 1)));
    }
}
