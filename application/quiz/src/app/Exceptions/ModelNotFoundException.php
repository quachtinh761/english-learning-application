<?php

namespace App\Exceptions;

class ModelNotFoundException extends \Exception
{
    public function __construct(string $model, int|string $id)
    {
        parent::__construct("Model $model with term $id not found");
    }
}
