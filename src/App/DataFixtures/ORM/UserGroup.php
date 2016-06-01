<?php
/**
 * /src/App/DataFixtures/ORM/UserGroup.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace App\DataFixtures\ORM;

use App\Entity\UserGroup as UserGroupEntity;
use App\Services\Helper\Roles;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class UserGroup
 *
 * This fixture will create following data to test environment database:
 *
 * -
 *  id: 1
 *  name: "Logged in users"
 *  role: "ROLE_LOGGED"
 * -
 *  id: 2
 *  name: "Normal users"
 *  role: "ROLE_USER"
 * -
 *  id: 3
 *  name: "Admin users"
 *  role: "ROLE_ADMIN"
 * -
 *  id: 4
 *  name: "Root users"
 *  role: "ROLE_ROOT"
 *
 * @category    Fixtures
 * @package     App\DataFixtures\ORM
 * @author      TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class UserGroup extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    // Traits
    use ContainerAwareTrait;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $roles = $this->container->get('app.services.helper.roles');

        foreach ($roles->getRoles() as $role) {
            // Create new user group
            $group = new UserGroupEntity();
            $group->setName($roles->getRoleLabel($role));
            $group->setRole($role);

            $manager->persist($group);

            // Create reference to current user group
            $this->addReference('user-group-' . $roles->getShort($role), $group);
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 0;
    }
}