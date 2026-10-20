<?php

namespace App\Models;

use App\Services\SmsService;
use Illuminate\Database\Eloquent\Model;

class AbandonedCart extends Model
{
    protected $fillable = [
        "phone", "customer_name", "items", "total", "token",
        "last_seen_at", "reminded_at", "recovered_at",
    ];

    protected $casts = [
        "items"        => "array",
        "total"        => "decimal:2",
        "last_seen_at" => "datetime",
        "reminded_at"  => "datetime",
        "recovered_at" => "datetime",
    ];

    /** Mark every open saved cart of this phone as recovered (an order was placed). */
    public static function markRecovered(?string $phone): void
    {
        $n = SmsService::normalizePhone($phone);

        if ($n === null) {
            return;
        }

        static::where("phone", $n)->whereNull("recovered_at")->update(["recovered_at" => now()]);
    }
}
