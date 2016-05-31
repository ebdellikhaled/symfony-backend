<?php
/**
 * /src/App/Repository/User.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace App\Repository;

use App\Entity;
use Doctrine\ORM\NoResultException;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Doctrine repository class for User entities.
 *
 * @category    Doctrine
 * @package     App\Repository
 * @author      TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class User extends Base implements UserProviderInterface, UserLoaderInterface
{
    /**
     * Names of search columns.
     *
     * @var string[]
     */
    protected $searchColumns = ['username', 'firstname', 'surname', 'email'];

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not found.
     *
     * Method is override for performance reasons see link below.
     *
     * @link http://symfony2-document.readthedocs.org/en/latest/cookbook/security/entity_provider.html
     *       #managing-roles-in-the-database
     *
     * @param   string  $username   The username
     *
     * @return  UserInterface
     *
     * @throws  UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        // Build query
        $query = $this
            ->createQueryBuilder('u')
            ->select('u, g')
            ->leftJoin('u.userGroups', 'g')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
        ;

        try {
            $user = $query->getSingleResult();
        } catch (NoResultException $exception) {
            $message = sprintf(
                'User "%s" not found',
                $username
            );

            throw new UsernameNotFoundException($message);
        }

        return $user;
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be totally reloaded (e.g. from the database),
     * or if the UserInterface object can just be merged into some internal array of users / identity map.
     *
     * @param   UserInterface   $user
     *
     * @return  UserInterface
     *
     * @throws  UnsupportedUserException    if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);

        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param   string  $class
     *
     * @return  bool
     */
    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
}
