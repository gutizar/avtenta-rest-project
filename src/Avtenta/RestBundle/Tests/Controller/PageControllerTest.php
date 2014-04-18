<?php

namespace Avtenta\RestBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase as WebTestCase;
use Avtenta\ModelBundle\Tests\Fixtures\Entity\LoadPageData;

class PageControllerTest extends WebTestCase
{
	public function setUp()
	{
		$this->auth = array(
        'PHP_AUTH_USER' => 'user',
        'PHP_AUTH_PW'   => 'userpass',
    	);

		$this->client = static::createClient(array(), $this->auth);
	}

	public function testGetAction()
	{
		$fixtures = array('Avtenta\ModelBundle\Tests\Fixtures\Entity\LoadPageData');
		$this->loadFixtures($fixtures);
		$page = array_pop(LoadPageData::$pages);
		
		$route = $this->getUrl('api_1_get_page', array('id' => $page->getId(), '_format' => 'json'));
		$this->client->request('GET', $route);
		$response = $this->client->getResponse();
		$this->assertJsonResponse($response, 200);
		$content = $response->getContent();

		$decoded = json_decode($content, true);
		$this->assertTrue(isset($decoded['id']));
	}

	public function testJsonPostAction()
	{
		$this->client->request(
			'POST',
			'/api/v1/pages.json',
			array(),
			array(),
			array('CONTENT_TYPE' => 'application/json'),
			'{"page":{"title":"title1","body":"body1"}}'
		);

		$this->assertJsonResponse($this->client->getResponse(), 201, false);
	}

	public function testJsonPostPageActionShouldReturn400WithBadParameters()
    {
        $this->client->request(
            'POST',
            '/api/v1/pages.json',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"page":{"titles":"title1","bodys":"body1"}}'
        );

        $this->assertJsonResponse($this->client->getResponse(), 400, false);
    }

    public function testJsonPutPageActionShouldModify()
	{
	    $fixtures = array('Avtenta\ModelBundle\Tests\Fixtures\Entity\LoadPageData');
	    $this->loadFixtures($fixtures);
	    $pages = LoadPageData::$pages;
	    $page = array_pop($pages);

	    $this->client->request('GET', sprintf('/api/v1/pages/%d.json', $page->getId()), array('ACCEPT' => 'application/json'));
	    $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

	    $this->client->request(
	        'PUT',
	        sprintf('/api/v1/pages/%d.json', $page->getId()),
	        array(),
	        array(),
	        array('CONTENT_TYPE' => 'application/json'),
	        '{"title":"abc","body":"def"}'
	    );

	    $this->assertJsonResponse($this->client->getResponse(), 204, false);
	    $this->assertTrue(
	        $this->client->getResponse()->headers->contains(
	            'Location',
	            sprintf('http://localhost/api/v1/pages/%d', $page->getId())
	        ),
	        $this->client->getResponse()->headers
	    );
	}

	public function testJsonPutPageActionShouldCreate()
	{
	    $id = 0;
	    $this->client->request('GET', sprintf('/api/v1/pages/%d.json', $id), array('ACCEPT' => 'application/json'));
	    $this->assertEquals(404, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

	    $this->client->request(
	        'PUT',
	        sprintf('/api/v1/pages/%d.json', $id),
	        array(),
	        array(),
	        array('CONTENT_TYPE' => 'application/json'),
	        '{"title":"abc","body":"def"}'
	    );

	    $this->assertJsonResponse($this->client->getResponse(), 201, false);
	}

	protected function assertJsonResponse($response, $statusCode = 200, $checkValidJson = true, $contentType = 'application/json')
	{
		$this->assertEquals(
			$statusCode, $response->getStatusCode(),
			$response->getContent()
		);

		$this->assertTrue(
            $response->headers->contains('Content-Type', $contentType),
            $response->headers
        );

        if ($checkValidJson) {
            $decode = json_decode($response->getContent());
            $this->assertTrue(($decode != null && $decode != false),
                'is response valid json: [' . $response->getContent() . ']'
            );
        }
	}
}