<?php

namespace App\Controllers;

class HashController extends BaseController
{
    public function hash(){
        return view('hash');
    }

    public function processHash(){
        $input = $this->request->getPost('input');
        dd($input);
        $hash = password_hash($input, PASSWORD_DEFAULT);
        return view('hash', ['hash' => $hash]);
    }
}