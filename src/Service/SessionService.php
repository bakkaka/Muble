<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class SessionService {

    public $session;

    public function __construct()
    {
        $this->session = new Session(new NativeSessionStorage(), new NamespacedAttributeBag());
    }

}
