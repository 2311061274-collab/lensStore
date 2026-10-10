<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function ($category) {
            if (empty($category->slug) && ! empty($category->name)) {
                $baseSlug = Str::slug($category->name);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->where('id', '!=', $category->id ?? 0)->exists()) {
                    $slug = $baseSlug.'-'.$counter++;
                }
                $category->slug = $slug;
            }
        });
    }

    /**
     * Danh sách sản phẩm thuộc danh mục
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
