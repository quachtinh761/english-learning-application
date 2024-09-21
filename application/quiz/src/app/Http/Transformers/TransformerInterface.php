<?php

namespace App\Http\Transformers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface TransformerInterface
{
    public static function transformItems(array $items): array;

    public static function transformItem(Model $item): Model|array;
}
