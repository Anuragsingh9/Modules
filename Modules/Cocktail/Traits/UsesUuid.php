<?php
namespace App\Traits;
use Ramsey\Uuid\Uuid;

trait UsesUuid {
    protected static function bootUuid() {
        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->primaryKey} = Uuid::uuid1()->toString();
            }
        });
    }
    
    public function getIncrementing() {
        return FALSE;
    }
    
    public function getKeyType() {
        return 'string';
    }
}
