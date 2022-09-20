<?php

namespace App\Controllers;

use Classes\Controller;

class ExempleController extends Controller{

    public function index($request){
       return view('index');
    }
}