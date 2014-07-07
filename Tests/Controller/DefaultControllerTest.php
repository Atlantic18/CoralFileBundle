<?php


/*
 * This file is part of the Coral package.
 *
 * (c) Frantisek Troster <frantisek.troster@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Coral\FileBundle\Tests\Controller;

use Doctrine\Common\DataFixtures\Loader;

use Coral\CoreBundle\Utility\JsonParser;
use Coral\CoreBundle\Test\JsonTestCase;

class DefaultControllerTest extends JsonTestCase
{
    public function __construct()
    {
        /**
         * Initially a database needs to be created or the very first run
         * of phpunit fails. setupBeforeClass couldn't be used as it is static.
         */
        $this->resetFixtures();
    }

    private function resetFixtures()
    {
        $this->loadFixtures(array(
            'Coral\CoreBundle\Tests\DataFixtures\ORM\MinimalSettingsData'
        ));
    }

    public function testAddNoContent()
    {
        $this->resetFixtures();
        $client = $this->doPostRequest(
            '/v1/file/add',
            '{ "filename": "some file.png", "mime_type": "images/png" }'
        );

        $this->assertIsJsonResponse($client);
        $this->assertIsStatusCode($client, 500);

        $jsonRequest  = new JsonParser($client->getResponse()->getContent());

        $this->assertEquals('failed', $jsonRequest->getMandatoryParam('status'));
        $this->assertRegExp('/mandatory/', $jsonRequest->getMandatoryParam('message'));
    }

    public function testAddAndDetail()
    {
        $this->resetFixtures();
        $fileContent = base64_encode("TEST_DATA");
        $client = $this->doPostRequest(
            '/v1/file/add',
            '{ "filename": "some file.png", "mime_type": "image/png", "param1": "value1", "content": "' . $fileContent . '" }'
        );

        $this->assertIsJsonResponse($client);
        $this->assertIsStatusCode($client, 201);

        $jsonRequest  = new JsonParser($client->getResponse()->getContent());

        // $this->assertEquals('ok', $jsonRequest->getMandatoryParam('message'));
        $this->assertEquals('ok', $jsonRequest->getMandatoryParam('status'));

        $id = $jsonRequest->getMandatoryParam('id');
        $this->assertGreaterThan(0, $id);
        $this->assertEquals('http://static.example.com/some-file-1.png', $jsonRequest->getMandatoryParam('uri'));

        //Get node detail
        $fileId = $jsonRequest->getMandatoryParam('id');
        $client = $this->doGetRequest('/v1/file/detail/' . $fileId);

        $this->assertIsJsonResponse($client);
        $this->assertIsStatusCode($client, 200);

        $jsonRequest  = new JsonParser($client->getResponse()->getContent());

        $this->assertEquals('ok', $jsonRequest->getMandatoryParam('status'));
        $this->assertEquals(1, count($jsonRequest->getMandatoryParam('items')));
        $this->assertEquals("some file.png", $jsonRequest->getMandatoryParam('items[0].filename'));
        $this->assertEquals("image/png", $jsonRequest->getMandatoryParam('items[0].mime_type'));
        $this->assertEquals('http://static.example.com/some-file-1.png', $jsonRequest->getMandatoryParam('items[0].uri'));
        $this->assertEquals("value1", $jsonRequest->getMandatoryParam('items[0].param1'));
        $this->assertFalse($jsonRequest->getOptionalParam('items[0].thumbnails'));

        //file not found for alternative account
        $client = $this->doAlternativeAccountGetRequest('/v1/file/detail/' . $fileId);
        $this->assertIsJsonResponse($client);
        $this->assertIsStatusCode($client, 404);
    }

    public function testAddDuplicateContent()
    {
        $this->resetFixtures();
        $fileContent = base64_encode("TEST_DATA");
        $client = $this->doPostRequest(
            '/v1/file/add',
            '{ "filename": "some file", "mime_type": "image/png", "param1": "value1", "content": "' . $fileContent . '" }'
        );
        $this->assertIsJsonResponse($client);
        $this->assertIsStatusCode($client, 201);
        $jsonRequest  = new JsonParser($client->getResponse()->getContent());
        $this->assertEquals('ok', $jsonRequest->getMandatoryParam('status'));
        $id = $jsonRequest->getMandatoryParam('id');

        $client = $this->doPostRequest(
            '/v1/file/add',
            '{ "filename": "other file.png", "mime_type": "image/jpeg", "content": "' . $fileContent . '" }'
        );
        $this->assertIsJsonResponse($client);
        $this->assertIsStatusCode($client, 200);
        $jsonRequest  = new JsonParser($client->getResponse()->getContent());
        $this->assertEquals('ok', $jsonRequest->getMandatoryParam('status'));
        $this->assertEquals($id, $jsonRequest->getMandatoryParam('id'));

        //LIST
        $client = $this->doGetRequest('/v1/file/list');

        $this->assertIsJsonResponse($client);
        $this->assertIsStatusCode($client, 200);

        $jsonRequest  = new JsonParser($client->getResponse()->getContent());

        $this->assertEquals('ok', $jsonRequest->getMandatoryParam('status'));
        $this->assertEquals(1, count($jsonRequest->getMandatoryParam('items')));
        $this->assertEquals("some file", $jsonRequest->getMandatoryParam('items[0].filename'));
        $this->assertEquals("image/png", $jsonRequest->getMandatoryParam('items[0].mime_type'));
        $this->assertEquals('http://static.example.com/some-file-1.png', $jsonRequest->getMandatoryParam('items[0].uri'));
        $this->assertEquals("value1", $jsonRequest->getMandatoryParam('items[0].param1'));
    }

    public function testList()
    {
        $this->resetFixtures();
        $fileContent = base64_encode("TEST_DATA");
        $client = $this->doPostRequest(
            '/v1/file/add',
            '{ "filename": "some file", "mime_type": "image/png", "param1": "value1", "content": "' . $fileContent . '" }'
        );
        $client = $this->doPostRequest(
            '/v1/file/add',
            '{ "filename": "other file.png", "mime_type": "image/jpeg", "content": "' . base64_encode("TEST_DATA2") . '" }'
        );

        //LIST
        $client = $this->doGetRequest('/v1/file/list');

        $this->assertIsJsonResponse($client);
        $this->assertIsStatusCode($client, 200);

        $jsonRequest  = new JsonParser($client->getResponse()->getContent());

        $this->assertEquals('ok', $jsonRequest->getMandatoryParam('status'));
        $this->assertEquals(2, count($jsonRequest->getMandatoryParam('items')));
        $this->assertEquals("some file", $jsonRequest->getMandatoryParam('items[0].filename'));
        $this->assertEquals("image/png", $jsonRequest->getMandatoryParam('items[0].mime_type'));
        $this->assertEquals('http://static.example.com/some-file-1.png', $jsonRequest->getMandatoryParam('items[0].uri'));
        $this->assertEquals("value1", $jsonRequest->getMandatoryParam('items[0].param1'));
        $this->assertEquals("other file.png", $jsonRequest->getMandatoryParam('items[1].filename'));
        $this->assertEquals("image/jpeg", $jsonRequest->getMandatoryParam('items[1].mime_type'));

        //another account receives empty content
        $client = $this->doAlternativeAccountGetRequest('/v1/file/list');

        $this->assertIsJsonResponse($client);
        $this->assertIsStatusCode($client, 200);

        $jsonRequest  = new JsonParser($client->getResponse()->getContent());

        $this->assertEquals('ok', $jsonRequest->getMandatoryParam('status'));
        $this->assertEquals(0, count($jsonRequest->getMandatoryParam('items')));
    }
}
