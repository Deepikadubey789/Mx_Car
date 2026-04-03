<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ChatSetting extends Model
{
    protected $table = 'chat_settings';
    protected $fillable = ['key', 'value', 'type', 'section', 'description'];
    public $timestamps = true;

    /**
     * Get setting value by key with caching
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("chat_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting?->value ?? $default;
        });
    }

    /**
     * Set setting value and clear cache
     */
    public static function set(string $key, $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value, 'updated_at' => now()]);
        Cache::forget("chat_setting_{$key}");
    }

    /**
     * Get all settings grouped by section
     */
    public static function getBySection(string $section)
    {
        return Cache::remember("chat_settings_section_{$section}", 3600, function () use ($section) {
            return self::where('section', $section)->get();
        });
    }
}
