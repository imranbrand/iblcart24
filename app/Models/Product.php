<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'compare_price',
        'stock',
        'image',
    ];

    /*
    |--------------------------------------------------------------------------
    | SALE PRICE
    |--------------------------------------------------------------------------
    | compare_price = the old / regular price shown crossed out.
    | The customer always pays `price`.
    */

    public function getIsOnSaleAttribute(): bool
    {
        return $this->compare_price !== null
            && (float) $this->compare_price > (float) $this->price
            && (float) $this->price > 0;
    }

    public function getDiscountPercentAttribute(): int
    {
        if (!$this->is_on_sale) {
            return 0;
        }

        return (int) round(
            (((float) $this->compare_price - (float) $this->price) / (float) $this->compare_price) * 100
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CATEGORY
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ORDER ITEMS
    |--------------------------------------------------------------------------
    */

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | REVIEWS
    |--------------------------------------------------------------------------
    */

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->reviews()->approved()->latest();
    }

    public function getAverageRatingAttribute(): float
    {
        return round((float) $this->approvedReviews()->avg('rating'), 1);
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | GALLERY IMAGES (in addition to the main `image` column)
    |--------------------------------------------------------------------------
    */

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /*
    |--------------------------------------------------------------------------
    | SEO HELPERS
    |--------------------------------------------------------------------------
    */

    public function getMetaDescriptionAttribute(): string
    {
        $text = trim(strip_tags((string) $this->description));

        if ($text === '') {
            return $this->name . ' - Buy online at IBL CART 24.';
        }

        return \Illuminate\Support\Str::limit($text, 155);
    }
}