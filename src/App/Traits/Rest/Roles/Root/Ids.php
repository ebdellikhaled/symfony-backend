<?php
declare(strict_types=1);
/**
 * /src/App/Traits/Rest/Roles/Admin/Ids.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace App\Traits\Rest\Roles\Root;

use App\Traits\Rest\Methods\Ids as IdsMethod;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Trait to add 'Ids' action for REST resources for 'ROLE_ROOT' users.
 *
 * @see \App\Traits\Rest\Methods\Ids for detailed documents.
 *
 * @package App\Traits\Rest
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
trait Ids
{
    use IdsMethod;

    /**
     * Find action for current resource.
     *
     * @Route("/ids")
     *
     * @Method({"GET"})
     *
     * @Security("has_role('ROLE_ROOT')")
     *
     * @param   Request $request
     *
     * @return  Response
     */
    public function ids(Request $request): Response
    {
        return $this->idsMethod($request);
    }
}
