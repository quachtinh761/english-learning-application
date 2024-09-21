<?php

namespace App\Http\Repositories;

use App\Exceptions\ModelNotFoundException;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function getList(array $filter, array $sort, int $page, int $perPage, array|string $select = ['*']): Paginator|LengthAwarePaginator;

    /**
     * @throws ModelNotFoundException
     */
    public function find(int $id, array|string $select = ['*']): Model;

    /**
     * @throws Exception
     */
    public function create(array $data): Model;

    /**
     * @throws Exception
     */
    public function update(int $id, array $data): Model;

    /**
     * @throws Exception
     */
    public function delete(int $id): bool;
}
