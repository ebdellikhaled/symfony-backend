<?php
/**
 * /tests/AppBundle/Controller/AuthControllerTest.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace AppBundle\Controller;

use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthControllerTest
 *
 * @category    Tests
 * @package     AppBundle\Controller
 * @author      TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class AuthControllerTest extends WebTestCase
{
    /**
     * Test that valid user gets token and refresh_token
     *
     * @dataProvider providerTestThatValidCredentialsWork
     *
     * @param   string  $username
     * @param   string  $password
     */
    public function testThatValidCredentialsWork($username, $password)
    {
        $client = static::createClient();
        $client->request('POST', '/auth/getToken', ['username' => $username, 'password' => $password]);

        // Check that HTTP status code is correct
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            'User login was not successfully.\n' . $client->getResponse()
        );

        // Get response object
        $response = json_decode($client->getResponse()->getContent());

        // Attributes that should be present...
        $attributes = [
            'token',
            'refresh_token',
        ];

        // Iterate expected attributes and check that those are present
        foreach ($attributes as $attribute) {
            $messageNotPresent = 'getToken did not return all expected attributes, missing \'' . $attribute . '\'.';
            $messageEmpty = 'Attribute \'' . $attribute . '\' is empty, this is fail...';

            $this->assertObjectHasAttribute($attribute, $response, $messageNotPresent);
            $this->assertNotEmpty($response->{$attribute}, $messageEmpty);
        }
    }

    /**
     * Test that invalid credentials does not work.
     *
     * @dataProvider providerTestThatInvalidCredentialsWontWork
     *
     * @param   string  $username
     * @param   string  $password
     */
    public function testThatInvalidCredentialsWontWork($username, $password)
    {
        $client = static::createClient();
        $client->request('POST', '/auth/getToken', ['username' => $username, 'password' => $password]);

        // Specify error message
        $message = [
            'Hmm, weird invalid user can log in the system - take this serious, very serious...',
            $client->getResponse()
        ];

        // Check that HTTP status code is correct
        $this->assertEquals(
            401,
            $client->getResponse()->getStatusCode(),
            implode('\n', $message)
        );
    }

    /**
     * Test that not supported HTTP methods returns 405.
     *
     * @dataProvider providerTestThatNotSupportedMethodsReturn405
     *
     * @param   string  $method
     * @param   integer $expectedStatusCode
     * @param   string  $expectedContent
     */
    public function testThatNotSupportedMethodsReturn405($method, $expectedStatusCode, $expectedContent)
    {
        $client = static::createClient();
        $client->request($method, '/auth/getToken');

        // Check that HTTP status code is correct
        $this->assertEquals(
            $expectedStatusCode,
            $client->getResponse()->getStatusCode(),
            'HTTP status code was not expected for method \'' . $method . '\'\n' . $client->getResponse()
        );

        // Check that actual response content is correct
        $this->assertEquals(
            $expectedContent,
            $client->getResponse()->getContent(),
            'HTTP response was not expected for method \'' . $method . '\'\n' . $client->getResponse()
        );
    }

    /**
     * Method to test that GET /auth/profile returns correct response without any Authorization header.
     *
     * Expected response is
     *  Status: 401
     *  Body: {"code":401,"message":"Invalid credentials"}
     */
    public function testThatGetAuthProfileReturns401WithoutToken()
    {
        $client = static::createClient();
        $client->request('GET', '/auth/profile');

        // Check that HTTP status code is correct
        $this->assertEquals(
            401,
            $client->getResponse()->getStatusCode(),
            'HTTP status code was not expected for /auth/profile request\n' . $client->getResponse()
        );

        $this->assertEquals(
            '{"code":401,"message":"Invalid credentials"}',
            $client->getResponse()->getContent(),
            'Response content was not expected for /auth/profile request\n' . $client->getResponse()
        );
    }

    /**
     * Method to test that GET /auth/profile returns correct response with invalid Authorization header.
     *
     * Expected response is
     *  Status: 401
     *  Body: {"code":401,"message":"Invalid JWT Token"}
     */
    public function testThatGetAuthProfileReturns401WithInvalidToken()
    {
        $client = static::createClient([], $this->getAuthService()->getAuthorizationHeaders('invalidtoken'));
        $client->request('GET', '/auth/profile');

        // Check that HTTP status code is correct
        $this->assertEquals(
            401,
            $client->getResponse()->getStatusCode(),
            'HTTP status code was not expected for /auth/profile request\n' . $client->getResponse()
        );

        $this->assertEquals(
            '{"code":401,"message":"Invalid JWT Token"}',
            $client->getResponse()->getContent(),
            'Response content was not expected for /auth/profile request\n' . $client->getResponse()
        );
    }

    /**
     * Method to test that when specified user makes GET /auth/profile request he/she will get expected response.
     *
     * @dataProvider providerTestThatValidCredentialsWork
     *
     * @param   string  $username
     * @param   string  $password
     *
     * @throws  \Exception
     */
    public function testThatGetAuthProfileReturnsExpectedData($username, $password)
    {
        $client = $this->getClient($username, $password);
        $client->request(
            'GET',
            '/auth/profile'
        );

        // Check that HTTP status code is correct
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            'HTTP status code was not expected for /auth/profile request\n' . $client->getResponse()
        );

        $profileData = json_decode($client->getResponse()->getContent());

        $attributes = [
            'id', 'username', 'firstname', 'surname', 'email', 'userGroups', 'createdAt', 'updatedAt',
        ];

        foreach ($attributes as $attribute) {
            $this->assertObjectHasAttribute($attribute, $profileData);
        }
    }

    /**
     * Data provider method for 'testThatValidCredentialsWork' test
     *
     * @return array
     */
    public function providerTestThatValidCredentialsWork()
    {
        return [
            ['john', 'doe'],
            ['john.doe@test.com', 'doe'],

            ['john-logged', 'doe-logged'],
            ['john.doe-logged@test.com', 'doe-logged'],

            ['john-user', 'doe-user'],
            ['john.doe-user@test.com', 'doe-user'],

            ['john-admin', 'doe-admin'],
            ['john.doe-admin@test.com', 'doe-admin'],

            ['john-root', 'doe-root'],
            ['john.doe-root@test.com', 'doe-root'],
        ];
    }

    /**
     * Data provider method for 'testThatInvalidCredentialsWontWork' test
     *
     * @return array
     */
    public function providerTestThatInvalidCredentialsWontWork()
    {
        return [
            [null, null],
            [null, 'a'],
            ['a', null],
            ['a', 'a'],
            ['', ''],
            ['john', 'doẽ'],
            ['john.doe@test.com', ''],
            ['joh n', 'doe'],
        ];
    }

    /**
     * Data provider method for 'testThatNotSupportedMethodsReturn405' test
     *
     * @return array
     */
    public function providerTestThatNotSupportedMethodsReturn405()
    {
        return [
            ['GET',     Response::HTTP_METHOD_NOT_ALLOWED, $this->getContent('GET')],
            ['HEAD',    Response::HTTP_METHOD_NOT_ALLOWED, ''],
            ['PUT',     Response::HTTP_METHOD_NOT_ALLOWED, $this->getContent('PUT')],
            ['DELETE',  Response::HTTP_METHOD_NOT_ALLOWED, $this->getContent('DELETE')],
            ['TRACE',   Response::HTTP_METHOD_NOT_ALLOWED, $this->getContent('TRACE')],
            ['OPTIONS', Response::HTTP_METHOD_NOT_ALLOWED, $this->getContent('OPTIONS')],
            ['CONNECT', Response::HTTP_METHOD_NOT_ALLOWED, $this->getContent('CONNECT')],
            ['PATCH',   Response::HTTP_METHOD_NOT_ALLOWED, $this->getContent('PATCH')],
        ];
    }

    /**
     * Helper method to get default "No route..." error message for specified HTTP method.
     *
     * @param   string  $method HTTP method
     *
     * @return  string
     */
    private function getContent($method)
    {
        return json_encode([
            'message'   => 'No route found for "' . $method . ' /auth/getToken": Method Not Allowed (Allow: POST)',
            'code'      => 0,
            'status'    => 405,
        ]);
    }
}
