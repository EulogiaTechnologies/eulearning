<?php

namespace Modules\Certificate\Entities;

use App\Traits\Tenantable;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use Tenantable;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

}
