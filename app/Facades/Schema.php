<?php

namespace App\Facades;

use database\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema as BaseSchema;

class Schema extends BaseSchema
{
    /**
     * 接続のスキーマ ビルダー インスタンスを取得
     *
     * @param  string|null  $name
     * @return Builder
     */
    public static function connection($name): Builder
    {
        /** @var \Illuminate\Database\Schema\Builder $builder */
        $builder = static::$app['db']->connection($name)->getSchemaBuilder();
        $builder->blueprintResolver(static function($table, $callback) {
            return new Blueprint($table, $callback);
        });
        return $builder;
    }

    /**
     * デフォルト接続のスキーマ ビルダー インスタンスを取得
     *
     * @return Builder
     */
    protected static function getFacadeAccessor(): Builder
    {
        /** @var \Illuminate\Database\Schema\Builder $builder */
        $builder = static::$app['db']->connection()->getSchemaBuilder();
        $builder->blueprintResolver(static function($table, $callback) {
            return new Blueprint($table, $callback);
        });
        return $builder;
    }

}
