<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key','value','type','group'])]
class Setting extends Model
{
    use HasFactory;

    #[Scope]
    protected function group(Builder $builder, string $group)
    {
        $builder->where('group', $group);
    }

    //Helper Methods
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return static::castvalue($setting->value, $setting->type);
    }

    public function set($key, $value, $type = 'string', $group = 'general')
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group
            ]
            );
    }

    protected static function castvalue($value, $type)
    {
        return match($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOL),
            'number' => is_numeric($value) ? (float)$value : $value,
            'json' => json_encode($value, true),
        };
    }
}