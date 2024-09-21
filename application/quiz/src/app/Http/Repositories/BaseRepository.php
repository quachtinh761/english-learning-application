<?php

namespace App\Http\Repositories;

use App\Exceptions\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements RepositoryInterface
{
    abstract public function getModel(): Model;

    public function newQuery(): Builder
    {
        return $this->getModel()->newQuery();
    }

    public function getList(array $filter, array $sort, int $page, int $perPage, array|string $select = ['*']): Paginator|LengthAwarePaginator
    {
        $query = $this->newQuery();

        return $query->paginate($perPage, $select, 'page', $page);
    }

    /**
     * @throws ModelNotFoundException
     */
    public function find(int $id, array|string $select = ['*']): Model
    {
        $model = $this->newQuery()->find($id, $select);

        if (!$model) {
            throw new ModelNotFoundException($this->getModel()->getTable(), $id);
        }

        return $model;
    }

    /**
     * @throws Exception
     */
    public function create(array $data): Model
    {
        return $this->getModel()->create($data);
    }

    /**
     * @throws Exception
     */
    public function update(int $id, array $data): Model
    {
        $model = $this->find($id);

        $model->update($data);

        return $model;
    }

    /**
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        $model = $this->find($id);

        return $model->delete();
    }
}
