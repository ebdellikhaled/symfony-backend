<?php
/**
 * /tests/AppBundle/Services/Rest/Helper/RequestTest.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace AppBundle\Services\Rest\Helper;

use App\Services\Rest\Helper\Request as RequestHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestTest
 *
 * @package AppBundle\Services\Rest\Helper
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class RequestTest extends KernelTestCase
{
    public function testThatGetOrderByReturnsNullWithoutParameter()
    {
        $fakeRequest = Request::create('/', 'GET');

        $this->assertNull(
            RequestHelper::getOrderBy($fakeRequest),
            'getOrderBy method did not return NULL as it should without any parameters'
        );
    }

    /**
     * @dataProvider dataProviderTestThatGetOrderByReturnsExpectedValue
     *
     * @param array $parameters
     * @param array $expected
     */
    public function testThatGetOrderByReturnsExpectedValue(array $parameters, array $expected)
    {
        $fakeRequest = Request::create('/', 'GET', $parameters);

        $this->assertEquals(
            $expected,
            RequestHelper::getOrderBy($fakeRequest),
            'getOrderBy method did not return expected value'
        );
    }

    public function testThatGetLimitReturnsNullWithoutParameter()
    {
        $fakeRequest = Request::create('/', 'GET');

        $this->assertNull(
            RequestHelper::getLimit($fakeRequest),
            'getLimit method did not return NULL as it should without any parameters'
        );
    }

    /**
     * @dataProvider dataProviderTestThatGetLimitReturnsExpectedValue
     *
     * @param   array   $parameters
     * @param   integer $expected
     */
    public function testThatGetLimitReturnsExpectedValue(array $parameters, int $expected)
    {
        $fakeRequest = Request::create('/', 'GET', $parameters);

        $actual = RequestHelper::getLimit($fakeRequest);

        $this->assertNotNull(
            $actual,
            'getLimit returned NULL and it should return an integer'
        );

        $this->assertEquals(
            $expected,
            $actual,
            'getLimit method did not return expected value'
        );
    }

    public function testThatGetOffsetReturnsNullWithoutParameter()
    {
        $fakeRequest = Request::create('/', 'GET');

        $this->assertNull(
            RequestHelper::getOffset($fakeRequest),
            'getOffset method did not return NULL as it should without any parameters'
        );
    }

    /**
     * @dataProvider dataProviderTestThatGetOffsetReturnsExpectedValue
     *
     * @param   array   $parameters
     * @param   integer $expected
     */
    public function testThatGetOffsetReturnsExpectedValue(array $parameters, int $expected)
    {
        $fakeRequest = Request::create('/', 'GET', $parameters);

        $actual = RequestHelper::getOffset($fakeRequest);

        $this->assertNotNull(
            $actual,
            'getOffset returned NULL and it should return an integer'
        );

        $this->assertEquals(
            $expected,
            $actual,
            'getOffset method did not return expected value'
        );
    }

    /**
     * Data provider method for 'testThatGetOrderByReturnsExpectedValue' test.
     *
     * @return array
     */
    public function dataProviderTestThatGetOrderByReturnsExpectedValue()
    {
        return [
            [
                ['order' => 'column1'],
                ['column1' => 'ASC'],
            ],
            [
                ['order' => '-column1'],
                ['column1' => 'DESC'],
            ],
            [
                ['order' => 't.column1'],
                ['t.column1' => 'ASC'],
            ],
            [
                ['order' => '-t.column1'],
                ['t.column1' => 'DESC'],
            ],
            [
                [
                    'order' => [
                        'column1' => 'ASC',
                    ],
                ],
                ['column1' => 'ASC'],
            ],
            [
                [
                    'order' => [
                        'column1' => 'DESC',
                    ],
                ],
                ['column1' => 'DESC'],
            ],
            [
                [
                    'order' => [
                        'column1' => 'foobar',
                    ],
                ],
                ['column1' => 'ASC'],
            ],
            [
                [
                    'order' => [
                        't.column1' => 'ASC',
                    ],
                ],
                ['t.column1' => 'ASC'],
            ],
            [
                [
                    'order' => [
                        't.column1' => 'DESC',
                    ],
                ],
                ['t.column1' => 'DESC'],
            ],
            [
                [
                    'order' => [
                        't.column1' => 'foobar',
                    ],
                ],
                ['t.column1' => 'ASC'],
            ],
            [
                [
                    'order' => [
                        'column1' => 'ASC',
                        'column2' => 'DESC',
                    ],
                ],
                [
                    'column1' => 'ASC',
                    'column2' => 'DESC',
                ],
            ],
            [
                [
                    'order' => [
                        't.column1' => 'ASC',
                        't.column2' => 'DESC',
                    ],
                ],
                [
                    't.column1' => 'ASC',
                    't.column2' => 'DESC',
                ],
            ],
            [
                [
                    'order' => [
                        't.column1' => 'ASC',
                        'column2' => 'ASC',
                    ],
                ],
                [
                    't.column1' => 'ASC',
                    'column2' => 'ASC',
                ],
            ],
            [
                [
                    'order' => [
                        'column1' => 'ASC',
                        'column2' => 'foobar',
                    ],
                ],
                [
                    'column1' => 'ASC',
                    'column2' => 'ASC',
                ],
            ],
        ];
    }

    /**
     * Data provider method for 'testThatGetLimitReturnsExpectedValue' test.
     *
     * @return array
     */
    public function dataProviderTestThatGetLimitReturnsExpectedValue()
    {
        return [
            [
                ['limit' => 10],
                10,
            ],
            [
                ['limit' => 'ddd'],
                0,
            ],
            [
                ['limit' => 'E10'],
                0,
            ],
            [
                ['limit' => -10],
                10,
            ],
        ];
    }

    /**
     * Data provider method for 'testThatGetOffsetReturnsExpectedValue' test.
     *
     * @return array
     */
    public function dataProviderTestThatGetOffsetReturnsExpectedValue()
    {
        return [
            [
                ['offset' => 10],
                10,
            ],
            [
                ['offset' => 'ddd'],
                0,
            ],
            [
                ['offset' => 'E10'],
                0,
            ],
            [
                ['offset' => -10],
                10,
            ],
        ];
    }
}
