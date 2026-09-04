<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Setting extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'settings';

    protected $fillable = [
        'group', 'key', 'value', 'type', 'label', 'description',
    ];

    /**
     * Get a setting value by key (optionally within a group).
     */
    public static function get(string $key, mixed $default = null, string $group = 'general'): mixed
    {
        $setting = static::where('group', $group)->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set/upsert a setting value.
     */
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value]
        );
    }

    public function scopeForGroup($query, string $group) { return $query->where('group', $group); }
}
