<?php
/**
 * Created by PhpStorm.
 * User: Mahmoud S. Siddiq
 * Date: 31-Jan-19
 * Time: 2:54 PM
 */

namespace KnpU\CodeBattle\Tests;


use Guzzle\Http\Client;
use PHPUnit_Framework_TestCase;

class ProgrammerControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testPost()
    {

        // create our http client (Guzzle)
        $client = new Client('http://localhost:8000', array(
            'request.options' => array(
                'exceptions' => false,
            )
        ));

        $nickname = 'ObjectOrienter'.rand(0, 999);
        $data = array(
            'nickname' => $nickname,
            'avatarNumber' => 5,
            'tagLine' => 'a test dev!'
        );

        $request = $client->post('/api/programmers', null, json_encode($data));
        $response = $request->send();

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $data = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('nickname', $data);


    }



}

