<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Requests;

class PMSPHPUnitTestController extends Controller{
  public function phpunit_api_get(){
    return response('Success', 200)->header('Content-Type', 'text/plain');
  }

  public function phpunit_api_post(){
    return response('Success', 200)->header('Content-Type', 'text/plain');
  }

  public function phpunit_mail_gun_api_get() {
    return response('Success', 200)->header('Content-Type', 'text/plain');
  }

  public function phpunit_mail_gun_api_post() {
    return response('Success', 200)->header('Content-Type', 'text/plain');
  }
}