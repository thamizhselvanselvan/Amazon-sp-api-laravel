<?php

namespace Tests\Pms;

use Tests\TestCase;
use Storage;
use Requests;

class MessagingTest extends TestCase
{
    public function test_mail_gun_api()
    {
        $this->get('/pms/phpunit/mailgun-api-get')->assertStatus(200);
    }
}
