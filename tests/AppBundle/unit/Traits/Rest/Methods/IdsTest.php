<?php
declare(strict_types = 1);
/**
 * /tests/AppBundle/unit/Traits/Rest/Methods/IdsTest.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace AppBundle\unit\Traits\Rest\Methods;

use App\Traits\Rest\Methods\Ids;
use App\Services\Rest\Helper\Interfaces\Response as RestHelperResponseInterface;
use App\Services\Rest\Interfaces\Base as ResourceServiceInterface;
use AppBundle\unit\Traits\Rest\Methods\Ids as IdsTestClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once dirname(__FILE__) . '/Ids.php';

/**
 * Class IdsTest
 *
 * @package AppBundle\unit\Traits\Rest\Methods
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class IdsTest extends KernelTestCase
{
    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage You cannot use App\Traits\Rest\Methods\Ids trait within class that does not implement App\Controller\Interfaces\RestController interface.
     */
    public function testThatTraitThrowsAnException()
    {
        $mock = $this->getMockForTrait(Ids::class);
        $request = Request::create('/ids', 'GET');

        $mock->idsMethod($request);
    }

    public function testThatTraitCallsServiceMethods()
    {
        $resourceService = $this->createMock(ResourceServiceInterface::class);
        $restHelperResponse = $this->createMock(RestHelperResponseInterface::class);

        /** @var IdsTestClass|\PHPUnit_Framework_MockObject_MockObject $idsTestClass */
        $idsTestClass = $this->getMockForAbstractClass(IdsTestClass::class, [$resourceService, $restHelperResponse]);

        // Create request and response
        $request = Request::create('/ids', 'GET');
        $response = Response::create('[]');

        $resourceService
            ->expects(static::once())
            ->method('getIds')
            ->withAnyParameters()
            ->willReturn([]);

        $restHelperResponse
            ->expects(static::once())
            ->method('createResponse')
            ->withAnyParameters()
            ->willReturn($response);

        $idsTestClass
            ->expects(static::once())
            ->method('getResourceService')
            ->willReturn($resourceService);

        $idsTestClass
            ->expects(static::once())
            ->method('getResponseService')
            ->willReturn($restHelperResponse);

        $idsTestClass->idsMethod($request);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     *
     * @dataProvider dataProviderTestThatTraitThrowsAnExceptionWithWrongHttpMethod
     *
     * @param   string  $httpMethod
     */
    public function testThatTraitThrowsAnExceptionWithWrongHttpMethod(string $httpMethod)
    {
        $resourceService = $this->createMock(ResourceServiceInterface::class);
        $restHelperResponse = $this->createMock(RestHelperResponseInterface::class);

        /** @var IdsTestClass|\PHPUnit_Framework_MockObject_MockObject $testClass */
        $testClass = $this->getMockForAbstractClass(IdsTestClass::class, [$resourceService, $restHelperResponse]);

        // Create request and response
        $request = Request::create('/ids', $httpMethod);

        $testClass->idsMethod($request)->getContent();
    }

    public function testThatTraitCallsProcessCriteriaIfItExists()
    {
        $resourceService = $this->createMock(ResourceServiceInterface::class);
        $restHelperResponse = $this->createMock(RestHelperResponseInterface::class);

        /** @var IdsTestClass|\PHPUnit_Framework_MockObject_MockObject $testClass */
        $testClass = $this->getMockForAbstractClass(
            IdsTestClass::class,
            [$resourceService, $restHelperResponse],
            '',
            true,
            true,
            true,
            ['processCriteria']
        );

        // Create request
        $request = Request::create('/ids', 'GET');

        $testClass
            ->expects(static::once())
            ->method('processCriteria')
            ->withAnyParameters()
        ;

        $testClass->idsMethod($request)->getContent();
    }

    /**
     * @return array
     */
    public function dataProviderTestThatTraitThrowsAnExceptionWithWrongHttpMethod(): array
    {
        return [
            ['HEAD'],
            ['POST'],
            ['PUT'],
            ['DELETE'],
            ['OPTIONS'],
            ['CONNECT'],
            ['foobar'],
        ];
    }
}
