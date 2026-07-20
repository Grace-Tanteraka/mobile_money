<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

abstract class BaseController extends Controller
{
    protected $helpers = ['url', 'form'];

    protected $session;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->session = Services::session();
    }
}