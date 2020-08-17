<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Auth;

class ProductsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Auth::user()->products()->orderBy('id','desc')->select('name', 'sku', 'product_type', 'price')->get();
    }
}