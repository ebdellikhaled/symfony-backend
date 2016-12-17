<?php
declare(strict_types = 1);
/**
 * /tests/AppBundle/Traits/Rest/Methods/FindTest.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace AppBundle\Traits\Rest\Methods;

use App\Traits\Rest\Methods\Find;
use App\Services\Rest\Helper\Interfaces\Response as RestHelperResponseInterface;
use App\Services\Rest\Interfaces\Base as ResourceServiceInterface;
use AppBundle\Traits\Rest\Methods\Find as FindTestClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once dirname(__FILE__) . '/Find.php';

/**
 * Class FindTest
 *
 * @package AppBundle\Traits\Rest\Methods
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class FindTest extends KernelTestCase
{
    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage You cannot use App\Traits\Rest\Methods\Find trait within class that does not implement App\Controller\Interfaces\RestController interface.
     */
    public function testThatTraitThrowsAnException()
    {
        $mock = $this->getMockForTrait(Find::class);
        $request = Request::create('/', 'GET');

        $mock->findMethod($request);
    }

    public function testThatTraitCallsServiceMethods()
    {
        $resourceService = $this->createMock(ResourceServiceInterface::class);
        $restHelperResponse = $this->createMock(RestHelperResponseInterface::class);

        /** @var FindTestClass|\PHPUnit_Framework_MockObject_MockObject $findTestClass */
        $findTestClass = $this->getMockForAbstractClass(FindTestClass::class, [$resourceService, $restHelperResponse]);

        // Create request and response
        $request = Request::create('/', 'GET');
        $response = Response::create('[]');

        $resourceService
            ->expects(static::once())
            ->method('find')
            ->withAnyParameters()
            ->willReturn([]);

        $restHelperResponse
            ->expects(static::once())
            ->method('createResponse')
            ->withAnyParameters()
            ->willReturn($response);

        $findTestClass
            ->expects(static::once())
            ->method('getResourceService')
            ->willReturn($resourceService);

        $findTestClass
            ->expects(static::once())
            ->method('getResponseService')
            ->willReturn($restHelperResponse);

        $findTestClass->findMethod($request);
    }
}
