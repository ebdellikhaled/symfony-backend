<?php
declare(strict_types = 1);
/**
 * /tests/AppBundle/integration/Services/Rest/UserTest.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace AppBundle\integration\Services\Rest;

use App\Services\Rest\User;
use App\Tests\RestServiceTestCase;

/**
 * Class UserTest
 *
 * @package AppBundle\integration\Services\Rest
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class UserTest extends RestServiceTestCase
{
    /**
     * @var User
     */
    protected $service;

    /**
     * @var string
     */
    protected $serviceName = 'app.services.rest.user';

    /**
     * @var string
     */
    protected $entityName = 'App\Entity\User';

    /**
     * @var string
     */
    protected $repositoryName = 'App\Repository\User';

    /**
     * {@inheritdoc}
     */
    protected $entityCount = 5;
}