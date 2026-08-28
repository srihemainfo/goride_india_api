<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            if (isset($model->password)) {
                unset($model->password);
            }
            self::audit('audit:created', $model);
        });

        static::updated(function (Model $model) {
            $model->attributes = array_merge($model->getChanges(), ['id' => $model->id]);

            self::audit('audit:updated', $model);
        });

        static::deleted(function (Model $model) {
            self::audit('audit:deleted', $model);
        });
    }

    protected static function audit($description, $model)
    {
        
        $check = Session::get('check');
        $con = $check[0]->db_key ?? 'mysql' ;
        $id = $check[0]->id ?? null ;
        
        AuditLog::create([
            'description'  => $description,
            'subject_id'   => $model->id ?? null,
            'subject_type' => sprintf('%s#%s', get_class($model), $model->id) ?? null,
            'user_id'      => $id ?? null,
            'properties'   => $model ?? null,
            'host'         => request()->ip() ?? null,
        ]);
    }
}
