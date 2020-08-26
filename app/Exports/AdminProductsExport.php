<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use DB;

class AdminProductsExport implements FromCollection
{
    private $store_id;
    private $status_product_id;

    public function __construct($store_id, $status_product_id)
    {
        $this->store_id = $store_id;
        $this->status_product_id = $status_product_id;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $headers = ['ID', 'SKU', 'Name', 'Price', 'Продавец', 'Категория', 'Создано', 'Product_type',
            'Created_by', 'Changed_by'];

        $products = DB::table('products')->orderBy('products.id','desc')
            ->leftJoin('users', 'products.user_id', 'users.id')
            ->join('categories', 'products.category_id', 'categories.id')
            ->select('products.id', 'products.sku', 'products.name', 'products.price',
                DB::raw('IF(users.name IS NULL, "Anor.tj", IF(users.shop_name IS NULL, users.name, users.shop_name)) as shop_name'),
                'categories.name as category_name', 'products.created_at', 'products.product_type',
                'products.created_by', 'products.changed_by');

        if (!is_null($this->store_id)) $products->where('products.user_id', $this->store_id);

        if (!is_null($this->status_product_id)) {
            $products->where('products.status', $this->status_product_id);

            if ($this->status_product_id === 0) {
                array_push($headers, 'Причина деактивации');
                $products->addSelect('products.issue_deactivate');
            }
        }

        return $products->get()->prepend((object)$headers);
    }
}
