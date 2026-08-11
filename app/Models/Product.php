<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
class Product extends Model{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'products'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // protected $hidden = [
    // ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
        
    // ];

    /**
     * Apply the common POS product search used by the search and scan screens.
     */
    public function scopePosSearch(Builder $query, string $searchTerm): Builder
    {
        $searchTerm = trim($searchTerm);
        $query->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('suppliers', 'products.supplier_id', '=', 'suppliers.id');

        if ($searchTerm === '' || strlen($searchTerm) > 100) {
            return $query->whereRaw('1 = 0');
        }

        $escapedSearchTerm = addcslashes($searchTerm, '\\%_');
        $containsSearchTerm = '%'.$escapedSearchTerm.'%';

        return $query
            ->where('products.status', '=', 1)
            ->where(function (Builder $searchQuery) use ($containsSearchTerm) {
                $searchQuery->where('products.sku', 'LIKE', $containsSearchTerm)
                    ->orWhere('products.barcode', 'LIKE', $containsSearchTerm)
                    ->orWhere('products.name', 'LIKE', $containsSearchTerm)
                    ->orWhere('products.receipt_short_name', 'LIKE', $containsSearchTerm)
                    ->orWhere('products.shelf_tag_short_name', 'LIKE', $containsSearchTerm)
                    ->orWhere('products.supplier_sku', 'LIKE', $containsSearchTerm)
                    ->orWhere('products.supplier_product_name', 'LIKE', $containsSearchTerm)
                    ->orWhere('products.style', 'LIKE', $containsSearchTerm)
                    ->orWhere('brands.name', 'LIKE', $containsSearchTerm)
                    ->orWhere('suppliers.name', 'LIKE', $containsSearchTerm);
            })
            ->orderByRaw(
                'CASE WHEN products.barcode = ? THEN 0 WHEN products.sku = ? THEN 1 WHEN products.supplier_sku = ? THEN 2 WHEN products.name = ? THEN 3 ELSE 4 END',
                [$searchTerm, $searchTerm, $searchTerm, $searchTerm]
            )
            ->orderBy('products.name', 'ASC')
            ->orderBy('products.id', 'ASC');
    }
}
