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
        $this->loadFixtures(array(
            'Coral\CoreBundle\Tests\DataFixtures\ORM\MinimalSettingsData'
        ));
    }

    public function testAddAndDetail()
    {
        $nodeId = 1;

        $client = $this->doPostRequest(
            '/v1/file/add',
            '{ "filename": "some file.png", "mime_type": "images/png", "param1": "value1", "content": "base64encodedFile" }'
        );

        $this->assertIsJsonResponse($client);
        $this->assertIsStatusCode($client, 201);

        $jsonRequest  = new JsonParser($client->getResponse()->getContent());

        $this->assertEquals('ok', $jsonRequest->getMandatoryParam('status'));

        $id = $jsonRequest->getMandatoryParam('id');
        $this->assertGreaterThan(0, $id);
        $this->assertEquals('http://static.example.com/images/85d7a6f5-some-file.png', $jsonRequest->getMandatoryParam('uri'));

        //Get node detail
        $client = $this->doGetRequest('/v1/file/detail/' . $jsonRequest->getMandatoryParam('id'));

        $this->assertIsJsonResponse($client);
        $this->assertIsStatusCode($client, 200);

        $jsonRequest  = new JsonParser($client->getResponse()->getContent());

        $this->assertEquals('ok', $jsonRequest->getMandatoryParam('status'));
        $this->assertEquals(1, count($jsonRequest->getMandatoryParam('items')));
        $this->assertEquals("some file.png", $jsonRequest->getMandatoryParam('items[0].filename'));
        $this->assertEquals("image/png", $jsonRequest->getMandatoryParam('items[0].mime_type'));
        $this->assertEquals('http://static.example.com/images/85d7a6f5-some-file.png', $jsonRequest->getMandatoryParam('items[0].uri'));
        $this->assertEquals("value1", $jsonRequest->getMandatoryParam('items[0].param1'));
        $this->assertTrue(null === $jsonRequest->getMandatoryParam('items[0].thumbnails'));
    }
}
