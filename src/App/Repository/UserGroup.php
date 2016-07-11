<?php
/**
 * /src/App/Repository/UserGroup.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace App\Repository;

use App\Entity;

/**
 * Doctrine repository class for UserGroup entities.
 *
 * @package App\Repository
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class UserGroup extends Base
{
    /**
     * Names of search columns.
     *
     * @var string[]
     */
    protected $searchColumns = ['name', 'role'];

    // Implement custom entity query methods here
}
