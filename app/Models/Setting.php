<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Shop settings saved from the admin panel.
 * Saved values override the defaults in config/shop.php (which come from .env).
 */
class Setting extends Model
{
    public const CACHE_KEY = 'shop_settings_v1';

    protected $fillable = ['key', 'value'];

    /**
     * setting key => [config key, type]
     */
    public const MAP = [
        'name'                     => ['shop.name', 'string'],
        'phone'                    => ['shop.phone', 'string'],
        'whatsapp'                 => ['shop.whatsapp', 'string'],
        'email'                    => ['shop.email', 'string'],
        'address'                  => ['shop.address', 'string'],
        'hours'                    => ['shop.hours', 'string'],
        'return_days'              => ['shop.return_days', 'int'],
        'delivery_inside_dhaka'    => ['shop.delivery.inside_dhaka', 'float'],
        'delivery_outside_dhaka'   => ['shop.delivery.outside_dhaka', 'float'],
        'free_delivery_above'      => ['shop.delivery.free_above', 'float'],
        'delivery_days_inside'     => ['shop.delivery.inside_days', 'string'],
        'delivery_days_outside'    => ['shop.delivery.outside_days', 'string'],
        'reviews_require_approval' => ['shop.reviews_require_approval', 'bool'],
        'announcement_enabled'     => ['shop.announcement_enabled', 'bool'],
        'announcement_text'        => ['shop.announcement_text', 'string'],
    ];

    public static function allValues(): array
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, function () {
                return static::query()->pluck('value', 'key')->all();
            });
        } catch (\Throwable $e) {
            // Cache not available: read straight from the database.
            return static::query()->pluck('value', 'key')->all();
        }
    }

    public static function applyToConfig(): void
    {
        foreach (self::allValues() as $key => $value) {
            if (!isset(self::MAP[$key]) || $value === null) {
                continue;
            }

            [$configKey, $type] = self::MAP[$key];

            config([$configKey => self::cast($value, $type)]);
        }
    }

    public static function saveMany(array $values): void
    {
        foreach ($values as $key => $value) {
            if (!isset(self::MAP[$key])) {
                continue;
            }

            static::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value === null ? null : (string) $value]
            );
        }

        try {
            Cache::forget(self::CACHE_KEY);
        } catch (\Throwable $e) {
            //
        }

        self::applyToConfig();
    }

    /**
     * WhatsApp number as digits with country code (01XXXXXXXXX -> 8801XXXXXXXXX).
     */
    public static function whatsappNumber(): string
    {
        $digits = preg_replace('/\D+/', '', (string) config('shop.whatsapp'));

        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = '88' . $digits;
        }

        return $digits;
    }

    private static function cast(string $value, string $type)
    {
        return match ($type) {
            'int'   => (int) $value,
            'float' => (float) $value,
            'bool'  => in_array(strtolower($value), ['1', 'true', 'on', 'yes'], true),
            default => $value,
        };
    }
}
