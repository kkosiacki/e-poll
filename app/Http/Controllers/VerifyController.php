<?php

namespace App\Http\Controllers;

use App\Services\VerifyService;
use Illuminate\Http\Request;

class VerifyController extends Controller
{
    /**
     * @var VerifyService
     */
    private $verify_service;


    /**
     * VerifyController constructor.
     */
    public function __construct(VerifyService $verify_service)
    {
        $this->verify_service = $verify_service;
    }

    public function verify(Request $request) {
       $file_name =  $request->file('file')->store('to_verify');

       $this->verify_service->verifyFile($file_name);
    }
}
