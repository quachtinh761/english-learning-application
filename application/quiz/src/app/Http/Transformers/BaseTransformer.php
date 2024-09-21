<?php

namespace App\Http\Transformers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseTransformer implements TransformerInterface
{
    public static function transformItems(array $items): array
    {
        return array_map(fn(Model $item) => static::transformItem($item), $items);
    }

    public static function transformItem(Model $item): Model|array
    {
        return $item;
    }
}
