<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'branch_id',
        'category_id',
        'name',
        'slug',
        'description',
        'enable_table_management',
        'table_management_settings',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'enable_table_management' => 'boolean',
        'table_management_settings' => 'array',
    ];

    /**
     * Get the branch that owns the department.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Get the category that owns the department.
     */
    public function category()
    {
        return $this->belongsTo(DepartmentCategory::class, 'category_id');
    }

    /**
     * Get the employees for the department.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the product types for the department.
     */
    public function productTypes()
    {
        return $this->hasMany(ProductType::class);
    }

    /**
     * Get the pages for the department.
     */
    public function pages()
    {
        return $this->hasMany(DepartmentPage::class);
    }

    /**
     * Get all tables for this department.
     */
    public function tables()
    {
        return $this->hasMany(Table::class);
    }

    /**
     * Check if table management is enabled.
     */
    public function hasTableManagement(): bool
    {
        return $this->enable_table_management === true;
    }

    /**
     * Get the products that belong to this department.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'department_product')
            ->withPivot(['is_available', 'department_price', 'sort_order'])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }
}
