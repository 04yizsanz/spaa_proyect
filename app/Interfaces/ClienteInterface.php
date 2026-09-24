<?php

namespace App\Interfaces;

interface ClienteInterface extends BaseInterface
{
    public function getByName (string $name);
    public function getByLastname (string $lastname);
    public function getByDocument (string $document);
}