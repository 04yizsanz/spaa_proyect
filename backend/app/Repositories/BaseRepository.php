<?php

namespace App\Repositories;

use App\Interfaces\BaseInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseInterface
{
    protected Model $model;

    /**
     * Columnas donde busca paginar() cuando se envía 'buscar'.
     * Cada repositorio define las suyas.
     *
     * @var array<int, string>
     */
    protected array $camposBusqueda = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function all()
    {
        return $this->getAll();
    }

    public function getById(int $id)
    {
        $registro = $this->model->find($id);

          if(!$registro) {
            return null;
        }

        return $registro;

    }

    public function find(int $id)
    {
        return $this->getById($id);
    }

    public function update(array $data, int $id)
    {
        $registro = $this->model->find($id); 


        if (! $registro) {
            return null;
        }

        $registro->update($data);


        return $registro->fresh();
    }

    public function delete(int $id)
    {
        $registro = $this->model->find($id); 

        if (!$registro) {
            return null;
        }

        return $registro->delete(); 
    }

    public function paginar(array $opciones = []): LengthAwarePaginator
    {
        $consulta = $this->model->newQuery()->with($opciones['con'] ?? []);

        if (($actor = $opciones['visiblePara'] ?? null) && method_exists($this->model, 'scopeVisiblePara')) {
            $consulta->visiblePara($actor);
        }

        foreach ($opciones['filtros'] ?? [] as $columna => $valor) {
            if ($valor !== null && $valor !== '') {
                $consulta->where($this->model->qualifyColumn($columna), $valor);
            }
        }

        $termino = trim((string) ($opciones['buscar'] ?? ''));
        if ($termino !== '') {
            $this->aplicarBusqueda($consulta, $termino);
        }

        [$columna, $direccion] = $opciones['orden'] ?? [$this->model->getKeyName(), 'desc'];
        $consulta->orderBy($columna, $direccion === 'asc' ? 'asc' : 'desc');

        return $consulta->paginate($opciones['porPagina'] ?? 15)->withQueryString();
    }

    /**
     * Búsqueda por coincidencia parcial en $camposBusqueda.
     * Un repositorio puede sobrescribirla (por ejemplo, para buscar en relaciones).
     */
    protected function aplicarBusqueda(Builder $consulta, string $termino): void
    {
        if ($this->camposBusqueda === []) {
            return;
        }

        $consulta->where(function (Builder $q) use ($termino) {
            foreach ($this->camposBusqueda as $campo) {
                $q->orWhere($campo, 'like', '%' . $termino . '%');
            }
        });
    }
}
