<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitOfMeasure extends Model
{
    protected $table = 'units_of_measure';

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'category',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all active units ordered by sort_order
     */
    public static function active()
    {
        return self::where('is_active', true)->orderBy('sort_order')->get();
    }

    /**
     * Get units by category
     */
    public static function byCategory($category)
    {
        return self::where('category', $category)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get categories
     */
    public static function categories()
    {
        return self::where('is_active', true)
            ->distinct()
            ->pluck('category')
            ->sort()
            ->toArray();
    }

    /**
     * Find by code
     */
    public static function findByCode($code)
    {
        return self::where('code', $code)->first();
    }
}
