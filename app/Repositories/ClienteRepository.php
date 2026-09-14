<?php

namespace App\Repositories;

use App\Interfaces\ClienteInterface;
use App\Models\Cliente;

class ClienteRepository extends BaseRepository implements ClienteInterface
{
    public function __construct(Cliente $model)
    {
        parent::__construct($model);
    }

    public function getByName(String $name)
    {
        return $this->model
            ->whereHas('usuario', function ($query) use ($name) {
                $query->where('nombre', $name);
            })
            ->get();
    }

    public function getByLastname(String $lastname)
    {
        return $this->model
            ->whereHas('usuario', function ($query) use ($lastname) {
                $query->where('apellido', $lastname);
            })
            ->get();
    }

    public function getByDocument(String $document)
    {
        return $this->model
            ->whereHas('usuario', function ($query) use ($document) {
                $query->where('documento', $document);
            })
            ->first();
    }
}

