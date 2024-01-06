<?php

namespace Modules\Setting\Entities;

use App\Scopes\LmsScope;
use App\Traits\Tenantable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class CookieSetting extends Model
{
    use Tenantable;

    protected $fillable = [];


    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            Cache::forget('cookie_' . SaasDomain());
            Cache::forget('CookieSetting' . SaasDomain());
        });

        self::updated(function ($model) {
            Cache::forget('cookie_' . SaasDomain());
            Cache::forget('CookieSetting' . SaasDomain());
        });

        self::deleted(function ($model) {
            Cache::forget('cookie_' . SaasDomain());
            Cache::forget('CookieSetting' . SaasDomain());
        });
        static::addGlobalScope(new LmsScope);
    }


    public static function getData()
    {
        return Cache::rememberForever('CookieSetting' . SaasDomain(), function () {
            return CookieSetting::first();
        });
    }
}
