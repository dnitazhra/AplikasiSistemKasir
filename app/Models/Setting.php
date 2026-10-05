<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key_name',
        'value',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        $setting = static::where('key_name', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, ?string $value): self
    {
        return static::updateOrCreate(
            ['key_name' => $key],
            ['value' => $value]
        );
    }
}
