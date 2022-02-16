<?php

namespace Tests\Pms;

use Tests\TestCase;
use Requests;
use URL;
use Exception;

class ApiTest extends TestCase
{
    public function test_api_get()
    {

        try{
            $response = $this->getJson('/api/v1/pub/testGetApi');
            $data = $response->getData();
            if($data->status){
                $this->assertTrue(true);
            }
        } catch (Exception $error) {
            $this->assertFalse("failed! get api test " . $error);
        }

    }

    public function test_api_post()
    {

        try {
            $response = $this->postJson('/api/v1/pub/testGetApi');
            $data = $response->getData();
            if ($data->status) {
                $this->assertTrue(true);
            }
        } catch (Exception $error) {
            $this->assertFalse("failed! post api test " . $error);
        }

    }
}
