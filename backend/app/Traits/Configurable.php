<?php

namespace App\Traits;

use App\Services\ConfigurationCenter\AuditService;

trait Configurable
{
    public static function bootConfigurable()
    {
        static::created(function ($model) {
            AuditService::log($model, 'create', null, $model->getAttributes());
            AuditService::createVersion($model, 'Initial creation');
        });

        static::updated(function ($model) {
            $old = array_intersect_key($model->getOriginal(), $model->getDirty());
            $new = $model->getDirty();

            AuditService::log($model, 'update', $old, $new);
            AuditService::createVersion($model, 'Manual update via Configuration Center');
        });

        static::deleted(function ($model) {
            AuditService::log($model, 'delete', $model->getAttributes(), null);
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                AuditService::log($model, 'restore', null, $model->getAttributes());
            });
        }
    }
}
