<?php

namespace App\Interfaces;

use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Collection;

interface ClienteInterface extends BaseInterface
{
    public function getByName (string $name);
    public function getByLastname (string $lastname);
    public function getByDocument (string $document);

    /**
     * Clientes que el usuario puede consultar (scope Cliente::visiblePara).
     */
    public function visiblesPara(Usuario $usuario): Collection;

    /**
     * Busca un cliente por id solo entre los que el usuario puede consultar.
     */
    public function buscarVisible(int $id, Usuario $usuario): ?Cliente;
}
