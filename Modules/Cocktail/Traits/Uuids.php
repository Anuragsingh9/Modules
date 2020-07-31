<?php
namespace Modules\Cocktail\Traits;
use Ramsey\Uuid\Uuid;

trait Uuids {
    protected static function bootUuids() {
        static::creating(function ($model) {
            foreach ($model->uuidColumns as $values) {
                $model->attributes[$values]= Uuid::uuid1()->toString();
             }
        });
    }
    
}

