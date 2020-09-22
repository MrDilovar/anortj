<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AdminProductsExport;
use App\Models\Childcategory;
use App\Models\Subcategory;
use Datatables;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Gallery;
use App\Models\Attribute;
use App\Models\Admin;
use App\Models\AttributeOption;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use Image;
use DB;
use Auth;


class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function test () {
        $url = public_path() . '/assets/images/galleries/1594715479e7c2ea3f_f884_11e8_80f1_001e67d1aaeb_e7c2ea40_f884_11e8_80f1_001e67d1aaeb.jpg';

        Image::canvas(1024, 1024)
            ->fill('#ffffff')
            ->insert(Image::make($url)->resize(1024, 1024,
                function($constraint) {$constraint->aspectRatio();}), 'center')
            ->insert(public_path() . '/assets/images/watermark/watermark.png')
            ->save(public_path() . '/assets/images/galleries/123.jpg');
    }

    //*** JSON Request
    public function datatables()
    {

         $datas = Product::orderBy('id','desc')->get();

         //--- Integrating This Collection Into Datatables
         return Datatables::of($datas)
                            ->editColumn('name', function(Product $data) {
                                
                                // $name = mb_strlen(strip_tags($data->name),'utf-8') > 30 ? mb_substr(strip_tags($data->name),0,30,'utf-8').'...' : strip_tags($data->name);
                                // $name = mb_strlen(strip_tags($data->name),'utf-8') > 30 ? mb_substr(strip_tags($data->name),0,30,'utf-8').'...' : strip_tags($data->name);
                                $name = $data->name;
                                $id = '<small>ID: <a href="'.route('front.product', $data->slug).'" target="_blank">'.sprintf("%'.08d",$data->id).'</a></small>';
                               // $id2 = $data->user_id != 0 ? ( count($data->user->products) > 0 ? '<small class="ml-2"> VENDOR: <a href="'.route('admin-vendor-show',$data->user_id).'" target="_blank">'.$data->user->shop_name.'</a></small>' : '' ) : '';
                                $id3 = $data->type == 'Physical' ?'<small class="ml-2"> SKU: <a href="'.route('front.product', $data->slug).'" target="_blank">'.$data->sku.'</a>' : '';
                                //return  $name.'<br>'.$id.$id3.$id2;
                                return  $name.'<br>'.$id.$id3;
                            })
                            ->editColumn('shop_name', function(Product $data) {
                                $shop_name ='Anor.tj';
                                if( $data->user_id != 0 ){
                                $user_data = User::where('id','=',$data->user_id)->first();
                                $shop_name = $user_data->shop_name;
                                }
                                return  $shop_name;
                            })
                            ->editColumn('category', function(Product $data) {
                              
                                return  $data->category->name;
                            })
                            ->editColumn('price', function(Product $data) {
                                $sign = Currency::where('is_default','=',1)->first();
                                
                                $price = $data->price;
                                $price = $price.' '.$sign->name ;
                                return  $price;
                            })
                            // ->editColumn('stock', function(Product $data) {
                            //     $stck = (string)$data->stock;
                            //     if($stck == "0")
                            //     return "Out Of Stock";
                            //     elseif($stck == null)
                            //     return "Unlimited";
                            //     else
                            //     return $data->stock;
                            // })
                            // ->addColumn('status', function(Product $data) {
                            //     if(Auth::guard('admin')->user()->role_id == 0 || Auth::guard('admin')->user()->role_id == 17){
                            //         $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                            //         $s = $data->status == 1 ? 'selected' : '';
                            //         $ns = $data->status == 0 ? 'selected' : '';
                            //         return '<div class="action-list"><select class="process select droplinks '.$class.'"><option data-val="1" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>Activated</option><option  data-val="0" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>Deactivated</option>/select></div>';
                            //     }
                            //     $s = $data->status == 1 ? 'selected' : '';
                            //     $ns = $data->status == 0 ? 'selected' : '';
                            //     $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                            //     return '<div class="action-list"><select class="process select droplinks '.$class.'"><option disabled data-val="1" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>Activated</option><option disabled  data-val="0" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>Deactivated</option>/select></div>';

                            // })
                                    
                            ->addColumn('status', function(Product $data) {
                                if(Auth::guard('admin')->user()->role_id == 0 || Auth::guard('admin')->user()->role_id == 17){
                                    $class = $data->status == 1 ? 'drop-success' : ($data->status === 2 ? 'drop-dark' : ($data->status === 3 ? 'bg-info' : 'drop-danger'));
                                    $s = $data->status == 1 ? 'selected' : '';
                                    $ns = $data->status == 0 ? 'selected' : '';
                                    $w = $data->status == 2 ? 'selected' : '';
                                    $ch = $data->status == 3 ? 'selected' : '';

                                    return '<div class="action-list"><select class="process select droplinks '.$class.'">
                                    <option data-val="1" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>Activated</option>
                                    <option data-val="0" value="'. route('admin-prod-status-deactivate',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>Deactivated</option>
                                    <option data-val="2" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 2]).'" '.$w.'>Waiting</option>
                                    <option data-val="3" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 3]).'" '.$ch.' disabled>Changed</option>
                                    </select></div>';
                                }
                                   
                                    $class = $data->status == 1 ? 'drop-success' : ($data->status === 2 ? 'drop-dark' : ($data->status === 3 ? 'bg-info' : 'drop-danger'));
                                    $s = $data->status == 1 ? 'selected' : '';
                                    $ns = $data->status == 0 ? 'selected' : '';
                                    $w = $data->status == 2 ? 'selected' : '';
                                    $ch = $data->status == 3 ? 'selected' : '';

                                    return '<div class="action-list"><select class="process select droplinks '.$class.'">
                                    <option disabled data-val="1" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>Activated</option>
                                    <option disabled data-val="0" value="'. route('admin-prod-status-deactivate',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>Deactivated</option>
                                    <option disabled data-val="2" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 2]).'" '.$w.'>Waiting</option>
                                    <option data-val="3" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 3]).'" '.$ch.' >Changed</option>
                                    </select></div>';
                            })

                            ->addColumn('action', function(Product $data) {
                                // if(Auth::guard('admin')->user()->role_id == 0 || Auth::guard('admin')->user()->role_id == 17){

                                $catalog = $data->type == 'Physical' ? ($data->is_catalog == 1 ? '<a href="javascript:;" data-href="' . route('admin-prod-catalog',['id1' => $data->id, 'id2' => 0]) . '" data-toggle="modal" data-target="#catalog-modal" class="delete"><i class="fas fa-trash-alt"></i> Remove Catalog</a>' : '<a href="javascript:;" data-href="'. route('admin-prod-catalog',['id1' => $data->id, 'id2' => 1]) .'" data-toggle="modal" data-target="#catalog-modal"> <i class="fas fa-plus"></i> Add To Catalog</a>') : '';
                                // return '<div class="godropdown"><button class="go-dropdown-toggle"> Actions<i class="fas fa-chevron-down"></i></button><div class="action-list"><a href="' . route('admin-prod-edit',$data->id) . '"> <i class="fas fa-edit"></i> Edit</a><a href="javascript" class="set-gallery" data-toggle="modal" data-target="#setgallery"><input type="hidden" value="'.$data->id.'"><i class="fas fa-eye"></i> View Gallery</a>'.$catalog.'<a data-href="' . route('admin-prod-feature',$data->id) . '" class="feature" data-toggle="modal" data-target="#modal2"> <i class="fas fa-star"></i> Highlight</a><a href="javascript:;" data-href="' . route('admin-prod-delete',$data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i> Delete</a></div></div>';

                                return '<div class="godropdown"><button class="go-dropdown-toggle"> Actions<i class="fas fa-chevron-down"></i></button><div class="action-list"><a href="' . route('admin-prod-edit',$data->id) . '"> <i class="fas fa-edit"></i> Edit</a><a href="javascript" class="set-gallery" data-toggle="modal" data-target="#setgallery"><input type="hidden" value="'.$data->id.'"><i class="fas fa-eye"></i> View Gallery</a>'.$catalog.'<a data-href="' . route('admin-prod-feature',$data->id) . '" class="feature" data-toggle="modal" data-target="#modal2"> <i class="fas fa-star"></i> Highlight</a><a href="javascript:;" data-href="' . route('admin-prod-delete',$data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i> Delete</a></div></div>';
                                // }
                            })
                            ->rawColumns(['name', 'status', 'action'])
                            ->toJson(); //--- Returning Json Data To Client Side
    }



    //*** JSON Request
    public function datatablesReport()
    {

        $datas = Product::where('product_type','=','normal')
                            ->where("created_by","!=","NULL")
                            ->select([
                                DB::raw("created_by as login"),
                                //  DB::raw("created_by as total_by_login"),
                                DB::raw('DATE_FORMAT(created_at,"%Y %m %d") as day'),
                                DB::raw('count(DATE_FORMAT(created_at,"%Y %m %d")) as by_day')
                            ])->groupBy('created_by')
                            ->groupBy( DB::raw('DATE_FORMAT(created_at,"%Y %m %d")'))
                            ->orderBy('day','desc')->get();

        $up_name = Admin::where('id','=',Auth::guard('admin')->user()->id)->first();
       
        //--- Integrating This Collection Into Datatables
        return Datatables::of($datas)
                            ->editColumn('login', function(Product $data) {   
                                
                                if(!empty($data->login)) return $data->login;
                            })

                            ->editColumn('total_add', function(Product $data) {
                                $ttl_data =  Product::where('created_by','=',$data->login)
                                                ->select([
                                                    DB::raw("count(created_by) as total_by_login")
                                                ])->groupBy('created_by')->get();

                                $ttl_count = ltrim($ttl_data->pluck('total_by_login'),'[');  
                                $ttl_count = rtrim($ttl_count,']');                            
                                return  $ttl_count;    
                            })

                            ->editColumn('created_at', function(Product $data) {
                              
                                return  $data->day;
                            })
                            ->editColumn('total_date', function(Product $data) {
                                return $data->by_day;  
                               
                            })
                            ->rawColumns(['login', 'total_add'])
                            ->toJson(); //--- Returning Json Data To Client Side
    }
    

        //*** JSON Request
        public function datatablesReportModerator()
        {
          
           
            $datas = Product::where('product_type','=','normal')
                                ->where('status','=',1)
                                ->where("changed_by","!=",'NULL' )
                                ->select([
                                    DB::raw("changed_by as login"),
                                    //  DB::raw("created_by as total_by_login"),
                                    DB::raw('DATE_FORMAT(updated_at,"%Y %m %d") as day'),
                                    DB::raw('count(DATE_FORMAT(updated_at,"%Y %m %d")) as by_day')
                                ])->groupBy('changed_by')
                                ->groupBy( DB::raw('DATE_FORMAT(updated_at,"%Y %m %d")'))
                                ->orderBy('day','desc')->get();
    
            //--- Integrating This Collection Into Datatables
            return Datatables::of($datas)
                                ->editColumn('login', function(Product $data) {   
                                    
                                    if(!empty($data->login) ) return $data->login;
                                })
    
                                ->editColumn('total_add', function(Product $data) {

                                    
                                    $ttl_data =  Product::where('changed_by','=',$data->login)
                                                     ->where('status','=',1)
                                                    ->select([
                                                        DB::raw("count(changed_by) as total_by_login")
                                                    ])->groupBy('changed_by')->get();
                                    $ttl_count = ltrim($ttl_data->pluck('total_by_login'),'[');  
                                    $ttl_count = rtrim($ttl_count,']');                            
                                    return  $ttl_count;    
                                })
    
                                ->editColumn('created_at', function(Product $data) {
                                  
                                    return  $data->day;
                                })
                                ->editColumn('total_date', function(Product $data) {
                                    return $data->by_day;  
                                   
                                })
                                ->rawColumns(['login', 'total_add'])
                                ->toJson(); //--- Returning Json Data To Client Side
        }


      //*** JSON Request
      public function activedatatables()
      {
           $datas = Product::where('status','=',1)->orderBy('id','desc')->get();
  
           //--- Integrating This Collection Into Datatables
           return Datatables::of($datas)
                              ->editColumn('name', function(Product $data) {
                                  $name = mb_strlen(strip_tags($data->name),'utf-8') > 50 ? mb_substr(strip_tags($data->name),0,50,'utf-8').'...' : strip_tags($data->name);
                                  $id = '<small>ID: <a href="'.route('front.product', $data->slug).'" target="_blank">'.sprintf("%'.08d",$data->id).'</a></small>';
                                 // $id2 = $data->user_id != 0 ? ( count($data->user->products) > 0 ? '<small class="ml-2"> VENDOR: <a href="'.route('admin-vendor-show',$data->user_id).'" target="_blank">'.$data->user->shop_name.'</a></small>' : '' ) : '';
  
                                  $id3 = $data->type == 'Physical' ?'<small class="ml-2"> SKU: <a href="'.route('front.product', $data->slug).'" target="_blank">'.$data->sku.'</a>' : '';
  
                                  return  $name.'<br>'.$id.$id3;
                              })
                              ->editColumn('price', function(Product $data) {
                                  $sign = Currency::where('is_default','=',1)->first();
                                  $price = round($data->price * $sign->value , 2);
                                  $price = $sign->sign.$price ;
                                  return  $price;
                              })
                              ->editColumn('category', function(Product $data) {
                              
                                return  $data->category->name;
                              })
                              ->editColumn('stock', function(Product $data) {
                                  $stck = (string)$data->stock;
                                  if($stck == "0")
                                  return "Out Of Stock";
                                  elseif($stck == null)
                                  return "Unlimited";
                                  else
                                  return $data->stock;
                              })
                              ->addColumn('status', function(Product $data) {
                                  if(Auth::guard('admin')->user()->role_id == 0 || Auth::guard('admin')->user()->role_id == 17){
                                    $class = $data->status == 1 ? 'drop-success' : ($data->status == 2 ? 'drop-dark' : 'drop-danger');
                                    $s = $data->status == 1 ? 'selected' : '';
                                    $ns = $data->status == 0 ? 'selected' : '';
                                    $w = $data->status == 2 ? 'selected' : '';

                                    return '<div class="action-list"><select class="process select droplinks '.$class.'"><option data-val="1" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>Activated</option><option data-val="0" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>Deactivated</option><option data-val="2" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 2]).'" '.$w.'>Waiting</option></select></div>';
                                  }
                                  $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                                  $s = $data->status == 1 ? 'selected' : '';
                                  $ns = $data->status == 0 ? 'selected' : '';
                                  return '<div class="action-list"><select class="process select droplinks '.$class.'"><option disabled data-val="1" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>Activated</option><<option disabled data-val="0" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>Deactivated</option>/select></div>';
                                  
                              })
                              ->addColumn('action', function(Product $data) {
                                  if(Auth::guard('admin')->user()->role_id == 0 || Auth::guard('admin')->user()->role_id == 17){
                                      $catalog = $data->type == 'Physical' ? ($data->is_catalog == 1 ? '<a href="javascript:;" data-href="' . route('admin-prod-catalog',['id1' => $data->id, 'id2' => 0]) . '" data-toggle="modal" data-target="#catalog-modal" class="delete"><i class="fas fa-trash-alt"></i> Remove Catalog</a>' : '<a href="javascript:;" data-href="'. route('admin-prod-catalog',['id1' => $data->id, 'id2' => 1]) .'" data-toggle="modal" data-target="#catalog-modal"> <i class="fas fa-plus"></i> Add To Catalog</a>') : '';
                                      return '<div class="godropdown"><button class="go-dropdown-toggle"> Actions<i class="fas fa-chevron-down"></i></button><div class="action-list"><a href="' . route('admin-prod-edit',$data->id) . '"> <i class="fas fa-edit"></i> Edit</a><a href="javascript" class="set-gallery" data-toggle="modal" data-target="#setgallery"><input type="hidden" value="'.$data->id.'"><i class="fas fa-eye"></i> View Gallery</a>'.$catalog.'<a data-href="' . route('admin-prod-feature',$data->id) . '" class="feature" data-toggle="modal" data-target="#modal2"> <i class="fas fa-star"></i> Highlight</a><a href="javascript:;" data-href="' . route('admin-prod-delete',$data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i> Delete</a></div></div>';
                                  }
                              })
                              ->rawColumns(['name', 'status', 'action'])
                              ->toJson(); //--- Returning Json Data To Client Side
      }

       //*** JSON Request
       public function waitingdatatables()
       {
            $datas = Product::where('status','=',2)->orderBy('id','desc')->get();
   
            //--- Integrating This Collection Into Datatables
            return Datatables::of($datas)
                               ->editColumn('name', function(Product $data) {
                                   $name = mb_strlen(strip_tags($data->name),'utf-8') > 50 ? mb_substr(strip_tags($data->name),0,50,'utf-8').'...' : strip_tags($data->name);
                                   $id = '<small>ID: <a href="'.route('front.product', $data->slug).'" target="_blank">'.sprintf("%'.08d",$data->id).'</a></small>';
                                  // $id2 = $data->user_id != 0 ? ( count($data->user->products) > 0 ? '<small class="ml-2"> VENDOR: <a href="'.route('admin-vendor-show',$data->user_id).'" target="_blank">'.$data->user->shop_name.'</a></small>' : '' ) : '';
   
                                   $id3 = $data->type == 'Physical' ?'<small class="ml-2"> SKU: <a href="'.route('front.product', $data->slug).'" target="_blank">'.$data->sku.'</a>' : '';
   
                                   return  $name.'<br>'.$id.$id3;
                               })
                               ->editColumn('price', function(Product $data) {
                                   $sign = Currency::where('is_default','=',1)->first();
                                   $price = round($data->price * $sign->value , 2);
                                   $price = $sign->sign.$price ;
                                   return  $price;
                               })
                               ->editColumn('category', function(Product $data) {
                              
                                return  $data->category->name;
                              })
                               ->editColumn('stock', function(Product $data) {
                                   $stck = (string)$data->stock;
                                   if($stck == "0")
                                   return "Out Of Stock";
                                   elseif($stck == null)
                                   return "Unlimited";
                                   else
                                   return $data->stock;
                               })
                               ->addColumn('status', function(Product $data) {
                                   if(Auth::guard('admin')->user()->role_id == 0 || Auth::guard('admin')->user()->role_id == 17){
                                     $class = $data->status == 1 ? 'drop-success' : ($data->status == 2 ? 'drop-dark' : 'drop-danger');
                                     $s = $data->status == 1 ? 'selected' : '';
                                     $ns = $data->status == 0 ? 'selected' : '';
                                     $w = $data->status == 2 ? 'selected' : '';
 
                                     return '<div class="action-list"><select class="process select droplinks '.$class.'"><option data-val="1" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>Activated</option><option data-val="0" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>Deactivated</option><option data-val="2" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 2]).'" '.$w.'>Waiting</option></select></div>';
                                   }
                                   $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                                   $s = $data->status == 1 ? 'selected' : '';
                                   $ns = $data->status == 0 ? 'selected' : '';
                                   return '<div class="action-list"><select class="process select droplinks '.$class.'"><option disabled data-val="1" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>Activated</option><<option disabled data-val="0" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>Deactivated</option>/select></div>';
                                   
                               })
                               ->addColumn('action', function(Product $data) {
                                   if(Auth::guard('admin')->user()->role_id == 0 || Auth::guard('admin')->user()->role_id == 17){
                                       $catalog = $data->type == 'Physical' ? ($data->is_catalog == 1 ? '<a href="javascript:;" data-href="' . route('admin-prod-catalog',['id1' => $data->id, 'id2' => 0]) . '" data-toggle="modal" data-target="#catalog-modal" class="delete"><i class="fas fa-trash-alt"></i> Remove Catalog</a>' : '<a href="javascript:;" data-href="'. route('admin-prod-catalog',['id1' => $data->id, 'id2' => 1]) .'" data-toggle="modal" data-target="#catalog-modal"> <i class="fas fa-plus"></i> Add To Catalog</a>') : '';
                                       return '<div class="godropdown"><button class="go-dropdown-toggle"> Actions<i class="fas fa-chevron-down"></i></button><div class="action-list"><a href="' . route('admin-prod-edit',$data->id) . '"> <i class="fas fa-edit"></i> Edit</a><a href="javascript" class="set-gallery" data-toggle="modal" data-target="#setgallery"><input type="hidden" value="'.$data->id.'"><i class="fas fa-eye"></i> View Gallery</a>'.$catalog.'<a data-href="' . route('admin-prod-feature',$data->id) . '" class="feature" data-toggle="modal" data-target="#modal2"> <i class="fas fa-star"></i> Highlight</a><a href="javascript:;" data-href="' . route('admin-prod-delete',$data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i> Delete</a></div></div>';
                                   }
                               })
                               ->rawColumns(['name', 'status', 'action'])
                               ->toJson(); //--- Returning Json Data To Client Side
       }


    
    public function changedatatables()
    {
            $datas = Product::where('status','=',3)->orderBy('id','desc')->get();

            //--- Integrating This Collection Into Datatables
            return Datatables::of($datas)
                ->editColumn('name', function(Product $data) {
                    $name = mb_strlen(strip_tags($data->name),'utf-8') > 50 ? mb_substr(strip_tags($data->name),0,50,'utf-8').'...' : strip_tags($data->name);
                    $id = '<small>ID: <a href="'.route('front.product', $data->slug).'" target="_blank">'.sprintf("%'.08d",$data->id).'</a></small>';
                   // $id2 = $data->user_id != 0 ? ( count($data->user->products) > 0 ? '<small class="ml-2"> VENDOR: <a href="'.route('admin-vendor-show',$data->user_id).'" target="_blank">'.$data->user->shop_name.'</a></small>' : '' ) : '';

                    $id3 = $data->type == 'Physical' ?'<small class="ml-2"> SKU: <a href="'.route('front.product', $data->slug).'" target="_blank">'.$data->sku.'</a>' : '';

                    return  $name.'<br>'.$id.$id3;
                })
                ->editColumn('price', function(Product $data) {
                    $sign = Currency::where('is_default','=',1)->first();
                    $price = round($data->price * $sign->value , 2);
                    $price = $sign->sign.$price ;
                    return  $price;
                })
                ->editColumn('category', function(Product $data) {

                    return  $data->category->name;
                })
                ->editColumn('stock', function(Product $data) {
                    $stck = (string)$data->stock;
                    if($stck == "0")
                        return "Out Of Stock";
                    elseif($stck == null)
                        return "Unlimited";
                    else
                        return $data->stock;
                })
                ->addColumn('status', function(Product $data) {
                    if(Auth::guard('admin')->user()->role_id == 0 || Auth::guard('admin')->user()->role_id == 17){
                        $class = $data->status == 1 ? 'drop-success' : ($data->status === 2 ? 'drop-dark' : ($data->status === 3 ? 'bg-info' : 'drop-danger'));
                        $s = $data->status == 1 ? 'selected' : '';
                        $ns = $data->status == 0 ? 'selected' : '';
                        $w = $data->status == 2 ? 'selected' : '';
                        $ch = $data->status == 3 ? 'selected' : '';

                        return '<div class="action-list"><select class="process select droplinks '.$class.'">
                        <option data-val="1" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>Activated</option>
                        <option data-val="0" value="'. route('admin-prod-status-deactivate',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>Deactivated</option>
                        <option data-val="2" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 2]).'" '.$w.'>Waiting</option>
                        <option data-val="3" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 3]).'" '.$ch.' disabled>Changed</option>
                        </select></div>';
                    }
                   
                    $class = $data->status == 1 ? 'drop-success' : ($data->status === 2 ? 'drop-dark' : ($data->status === 3 ? 'bg-info' : 'drop-danger'));
                    $s = $data->status == 1 ? 'selected' : '';
                    $ns = $data->status == 0 ? 'selected' : '';
                    $w = $data->status == 2 ? 'selected' : '';
                    $ch = $data->status == 3 ? 'selected' : '';

                    return '<div class="action-list"><select class="process select droplinks '.$class.'">
                    <option disabled data-val="1" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>Activated</option>
                    <option disabled data-val="0" value="'. route('admin-prod-status-deactivate',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>Deactivated</option>
                    <option disabled data-val="2" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 2]).'" '.$w.'>Waiting</option>
                    <option data-val="3" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 3]).'" '.$ch.' >Changed</option>
                    </select></div>';
                })
                ->addColumn('action', function(Product $data) {
                    if(Auth::guard('admin')->user()->role_id == 0 || Auth::guard('admin')->user()->role_id == 17){
                        $catalog = $data->type == 'Physical' ? ($data->is_catalog == 1 ? '<a href="javascript:;" data-href="' . route('admin-prod-catalog',['id1' => $data->id, 'id2' => 0]) . '" data-toggle="modal" data-target="#catalog-modal" class="delete"><i class="fas fa-trash-alt"></i> Remove Catalog</a>' : '<a href="javascript:;" data-href="'. route('admin-prod-catalog',['id1' => $data->id, 'id2' => 1]) .'" data-toggle="modal" data-target="#catalog-modal"> <i class="fas fa-plus"></i> Add To Catalog</a>') : '';
                        return '<div class="godropdown"><button class="go-dropdown-toggle"> Actions<i class="fas fa-chevron-down"></i></button><div class="action-list"><a href="' . route('admin-prod-edit',$data->id) . '"> <i class="fas fa-edit"></i> Edit</a><a href="javascript" class="set-gallery" data-toggle="modal" data-target="#setgallery"><input type="hidden" value="'.$data->id.'"><i class="fas fa-eye"></i> View Gallery</a>'.$catalog.'<a data-href="' . route('admin-prod-feature',$data->id) . '" class="feature" data-toggle="modal" data-target="#modal2"> <i class="fas fa-star"></i> Highlight</a><a href="javascript:;" data-href="' . route('admin-prod-delete',$data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i> Delete</a></div></div>';
                    }
                })
                ->rawColumns(['name', 'status', 'action'])
                ->toJson(); //--- Returning Json Data To Client Side
    }   

    //*** JSON Request
    public function deactivedatatables()
    {
         $datas = Product::where('status','=',0)->orderBy('id','desc')->get();

         //--- Integrating This Collection Into Datatables
         return Datatables::of($datas)
                            ->editColumn('name', function(Product $data) {
                                $name = mb_strlen(strip_tags($data->name),'utf-8') > 50 ? mb_substr(strip_tags($data->name),0,50,'utf-8').'...' : strip_tags($data->name);
                                $id = '<small>ID: <a href="'.route('front.product', $data->slug).'" target="_blank">'.sprintf("%'.08d",$data->id).'</a></small>';
                                //$id2 = $data->user_id != 0 ? ( count($data->user->products) > 0 ? '<small class="ml-2"> VENDOR: <a href="'.route('admin-vendor-show',$data->user_id).'" target="_blank">'.$data->user->shop_name.'</a></small>' : '' ) : '';

                                $id3 = $data->type == 'Physical' ?'<small class="ml-2"> SKU: <a href="'.route('front.product', $data->slug).'" target="_blank">'.$data->sku.'</a>' : '';

                                return  $name.'<br>'.$id.$id3;
                            })
                            ->editColumn('price', function(Product $data) {
                                $sign = Currency::where('is_default','=',1)->first();
                                $price = round($data->price * $sign->value , 2);
                                $price = $sign->sign.$price ;
                                return  $price;
                            })
                            ->editColumn('category', function(Product $data) {
                              
                                return  $data->category->name;
                              })
                            ->editColumn('stock', function(Product $data) {
                                $stck = (string)$data->stock;
                                if($stck == "0")
                                return "Out Of Stock";
                                elseif($stck == null)
                                return "Unlimited";
                                else
                                return $data->stock;
                            })
                            ->addColumn('status', function(Product $data) {
                                if(Auth::guard('admin')->user()->role_id == 0 || Auth::guard('admin')->user()->role_id == 17){
                                    $class = $data->status == 1 ? 'drop-success' : ($data->status == 2 ? 'drop-dark' : 'drop-danger');
                                    $s = $data->status == 1 ? 'selected' : '';
                                    $ns = $data->status == 0 ? 'selected' : '';
                                    $w = $data->status == 2 ? 'selected' : '';

                                    return '<div class="action-list"><select class="process select droplinks '.$class.'"><option data-val="1" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>Activated</option><option data-val="0" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>Deactivated</option><option data-val="2" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 2]).'" '.$w.'>Waiting</option></select></div>';
                                }
                                $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                                $s = $data->status == 1 ? 'selected' : '';
                                $ns = $data->status == 0 ? 'selected' : '';
                                return '<div class="action-list"><select class="process select droplinks '.$class.'"><option disabled data-val="1" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>Activated</option><<option disabled data-val="0" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>Deactivated</option>/select></div>';
                                
                            })
                            ->addColumn('action', function(Product $data) {
                                if(Auth::guard('admin')->user()->role_id == 0 || Auth::guard('admin')->user()->role_id == 17){
                                    $catalog = $data->type == 'Physical' ? ($data->is_catalog == 1 ? '<a href="javascript:;" data-href="' . route('admin-prod-catalog',['id1' => $data->id, 'id2' => 0]) . '" data-toggle="modal" data-target="#catalog-modal" class="delete"><i class="fas fa-trash-alt"></i> Remove Catalog</a>' : '<a href="javascript:;" data-href="'. route('admin-prod-catalog',['id1' => $data->id, 'id2' => 1]) .'" data-toggle="modal" data-target="#catalog-modal"> <i class="fas fa-plus"></i> Add To Catalog</a>') : '';
                                    return '<div class="godropdown"><button class="go-dropdown-toggle"> Actions<i class="fas fa-chevron-down"></i></button><div class="action-list"><a href="' . route('admin-prod-edit',$data->id) . '"> <i class="fas fa-edit"></i> Edit</a><a href="javascript" class="set-gallery" data-toggle="modal" data-target="#setgallery"><input type="hidden" value="'.$data->id.'"><i class="fas fa-eye"></i> View Gallery</a>'.$catalog.'<a data-href="' . route('admin-prod-feature',$data->id) . '" class="feature" data-toggle="modal" data-target="#modal2"> <i class="fas fa-star"></i> Highlight</a><a href="javascript:;" data-href="' . route('admin-prod-delete',$data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i> Delete</a></div></div>';
                                }
                            })
                            ->rawColumns(['name', 'status', 'action'])
                            ->toJson(); //--- Returning Json Data To Client Side
    }

     //*** JSON Request
     public function discountdatatables()
     {
          $datas = Product::where('is_discount','=',1)->orderBy('is_discount','desc')->get();
 
          //--- Integrating This Collection Into Datatables
          return Datatables::of($datas)
                             ->editColumn('name', function(Product $data) {
                                 $name = mb_strlen(strip_tags($data->name),'utf-8') > 50 ? mb_substr(strip_tags($data->name),0,50,'utf-8').'...' : strip_tags($data->name);
                                 $id = '<small>ID: <a href="'.route('front.product', $data->slug).'" target="_blank">'.sprintf("%'.08d",$data->id).'</a></small>';
                                 //$id2 = $data->user_id != 0 ? ( count($data->user->products) > 0 ? '<small class="ml-2"> VENDOR: <a href="'.route('admin-vendor-show',$data->user_id).'" target="_blank">'.$data->user->shop_name.'</a></small>' : '' ) : '';
 
                                 $id3 = $data->type == 'Physical' ?'<small class="ml-2"> SKU: <a href="'.route('front.product', $data->slug).'" target="_blank">'.$data->sku.'</a>' : '';
 
                                 return  $name.'<br>'.$id.$id3;
                             })
                             ->editColumn('price', function(Product $data) {
                                 $sign = Currency::where('is_default','=',1)->first();
                                 $price = round($data->price * $sign->value , 2);
                                 $price = $sign->sign.$price ;
                                 return  $price;
                             })
                             ->editColumn('category', function(Product $data) {
                            
                              return  $data->category->name;
                            })
                             ->editColumn('stock', function(Product $data) {
                                 $stck = (string)$data->stock;
                                 if($stck == "0")
                                 return "Out Of Stock";
                                 elseif($stck == null)
                                 return "Unlimited";
                                 else
                                 return $data->stock;
                             })
                             ->addColumn('status', function(Product $data) {
                                 if(Auth::guard('admin')->user()->role_id == 0 || Auth::guard('admin')->user()->role_id == 17){
                                   $class = $data->status == 1 ? 'drop-success' : ($data->status == 2 ? 'drop-dark' : 'drop-danger');
                                   $s = $data->status == 1 ? 'selected' : '';
                                   $ns = $data->status == 0 ? 'selected' : '';
                                   $w = $data->status == 2 ? 'selected' : '';

                                   return '<div class="action-list"><select class="process select droplinks '.$class.'"><option data-val="1" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>Activated</option><option data-val="0" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>Deactivated</option><option data-val="2" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 2]).'" '.$w.'>Waiting</option></select></div>';
                                 }
                                 $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                                 $s = $data->status == 1 ? 'selected' : '';
                                 $ns = $data->status == 0 ? 'selected' : '';
                                 return '<div class="action-list"><select class="process select droplinks '.$class.'"><option disabled data-val="1" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>Activated</option><<option disabled data-val="0" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>Deactivated</option>/select></div>';
                                 
                             })
                             ->addColumn('action', function(Product $data) {
                                 if(Auth::guard('admin')->user()->role_id == 0 || Auth::guard('admin')->user()->role_id == 17){
                                     $catalog = $data->type == 'Physical' ? ($data->is_catalog == 1 ? '<a href="javascript:;" data-href="' . route('admin-prod-catalog',['id1' => $data->id, 'id2' => 0]) . '" data-toggle="modal" data-target="#catalog-modal" class="delete"><i class="fas fa-trash-alt"></i> Remove Catalog</a>' : '<a href="javascript:;" data-href="'. route('admin-prod-catalog',['id1' => $data->id, 'id2' => 1]) .'" data-toggle="modal" data-target="#catalog-modal"> <i class="fas fa-plus"></i> Add To Catalog</a>') : '';
                                     return '<div class="godropdown"><button class="go-dropdown-toggle"> Actions<i class="fas fa-chevron-down"></i></button><div class="action-list"><a href="' . route('admin-prod-edit',$data->id) . '"> <i class="fas fa-edit"></i> Edit</a><a href="javascript" class="set-gallery" data-toggle="modal" data-target="#setgallery"><input type="hidden" value="'.$data->id.'"><i class="fas fa-eye"></i> View Gallery</a>'.$catalog.'<a data-href="' . route('admin-prod-feature',$data->id) . '" class="feature" data-toggle="modal" data-target="#modal2"> <i class="fas fa-star"></i> Highlight</a><a href="javascript:;" data-href="' . route('admin-prod-delete',$data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i> Delete</a></div></div>';
                                 }
                             })
                             ->rawColumns(['name', 'status', 'action'])
                             ->toJson(); //--- Returning Json Data To Client Side
     }

     //*** JSON Request
     public function featureddatatables()
     {
          $datas = Product::where('featured','=',1)->where('status','=',1)->orderBy('featured','desc')->get();
 
          //--- Integrating This Collection Into Datatables
          return Datatables::of($datas)
                             ->editColumn('name', function(Product $data) {
                                 $name = mb_strlen(strip_tags($data->name),'utf-8') > 50 ? mb_substr(strip_tags($data->name),0,50,'utf-8').'...' : strip_tags($data->name);
                                 $id = '<small>ID: <a href="'.route('front.product', $data->slug).'" target="_blank">'.sprintf("%'.08d",$data->id).'</a></small>';
                                // $id2 = $data->user_id != 0 ? ( count($data->user->products) > 0 ? '<small class="ml-2"> VENDOR: <a href="'.route('admin-vendor-show',$data->user_id).'" target="_blank">'.$data->user->shop_name.'</a></small>' : '' ) : '';
 
                                 $id3 = $data->type == 'Physical' ?'<small class="ml-2"> SKU: <a href="'.route('front.product', $data->slug).'" target="_blank">'.$data->sku.'</a>' : '';
 
                                 return  $name.'<br>'.$id.$id3;
                             })
                             ->editColumn('price', function(Product $data) {
                                 $sign = Currency::where('is_default','=',1)->first();
                                 $price = round($data->price * $sign->value , 2);
                                 $price = $sign->sign.$price ;
                                 return  $price;
                             })
                             ->editColumn('category', function(Product $data) {
                            
                              return  $data->category->name;
                            })
                             ->editColumn('stock', function(Product $data) {
                                 $stck = (string)$data->stock;
                                 if($stck == "0")
                                 return "Out Of Stock";
                                 elseif($stck == null)
                                 return "Unlimited";
                                 else
                                 return $data->stock;
                             })
                             ->addColumn('status', function(Product $data) {
                                 if(Auth::guard('admin')->user()->role_id == 0 || Auth::guard('admin')->user()->role_id == 17){
                                   $class = $data->status == 1 ? 'drop-success' : ($data->status == 2 ? 'drop-dark' : 'drop-danger');
                                   $s = $data->status == 1 ? 'selected' : '';
                                   $ns = $data->status == 0 ? 'selected' : '';
                                   $w = $data->status == 2 ? 'selected' : '';

                                   return '<div class="action-list"><select class="process select droplinks '.$class.'"><option data-val="1" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>Activated</option><option data-val="0" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>Deactivated</option><option data-val="2" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 2]).'" '.$w.'>Waiting</option></select></div>';
                                 }
                                 $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                                 $s = $data->status == 1 ? 'selected' : '';
                                 $ns = $data->status == 0 ? 'selected' : '';
                                 return '<div class="action-list"><select class="process select droplinks '.$class.'"><option disabled data-val="1" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>Activated</option><<option disabled data-val="0" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>Deactivated</option>/select></div>';
                                 
                             })
                             ->addColumn('action', function(Product $data) {
                                 if(Auth::guard('admin')->user()->role_id == 0 || Auth::guard('admin')->user()->role_id == 17){
                                     $catalog = $data->type == 'Physical' ? ($data->is_catalog == 1 ? '<a href="javascript:;" data-href="' . route('admin-prod-catalog',['id1' => $data->id, 'id2' => 0]) . '" data-toggle="modal" data-target="#catalog-modal" class="delete"><i class="fas fa-trash-alt"></i> Remove Catalog</a>' : '<a href="javascript:;" data-href="'. route('admin-prod-catalog',['id1' => $data->id, 'id2' => 1]) .'" data-toggle="modal" data-target="#catalog-modal"> <i class="fas fa-plus"></i> Add To Catalog</a>') : '';
                                     return '<div class="godropdown"><button class="go-dropdown-toggle"> Actions<i class="fas fa-chevron-down"></i></button><div class="action-list"><a href="' . route('admin-prod-edit',$data->id) . '"> <i class="fas fa-edit"></i> Edit</a><a href="javascript" class="set-gallery" data-toggle="modal" data-target="#setgallery"><input type="hidden" value="'.$data->id.'"><i class="fas fa-eye"></i> View Gallery</a>'.$catalog.'<a data-href="' . route('admin-prod-feature',$data->id) . '" class="feature" data-toggle="modal" data-target="#modal2"> <i class="fas fa-star"></i> Highlight</a><a href="javascript:;" data-href="' . route('admin-prod-delete',$data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i> Delete</a></div></div>';
                                 }
                             })
                             ->rawColumns(['name', 'status', 'action'])
                             ->toJson(); //--- Returning Json Data To Client Side
     }

    //*** JSON Request
    public function catalogdatatables()
    {
         $datas = Product::where('is_catalog','=',1)->orderBy('id','desc')->get();
           
         //--- Integrating This Collection Into Datatables
         return Datatables::of($datas)
                            ->editColumn('name', function(Product $data) {
                                $name = mb_strlen(strip_tags($data->name),'utf-8') > 50 ? mb_substr(strip_tags($data->name),0,50,'utf-8').'...' : strip_tags($data->name);
                                $id = '<small>ID: <a href="'.route('front.product', $data->slug).'" target="_blank">'.sprintf("%'.08d",$data->id).'</a></small>';

                                $id3 = $data->type == 'Physical' ?'<small class="ml-2"> SKU: <a href="'.route('front.product', $data->slug).'" target="_blank">'.$data->sku.'</a>' : '';

                                return  $name.'<br>'.$id.$id3;
                            })
                            ->editColumn('price', function(Product $data) {
                                $sign = Currency::where('is_default','=',1)->first();
                                $price = round($data->price * $sign->value , 2);
                                $price = $sign->sign.$price ;
                                return  $price;
                            })
                            ->editColumn('stock', function(Product $data) {
                                $stck = (string)$data->stock;
                                if($stck == "0")
                                return "Out Of Stock";
                                elseif($stck == null)
                                return "Unlimited";
                                else
                                return $data->stock;
                            })
                            ->addColumn('status', function(Product $data) {
                                if(Auth::guard('admin')->user()->role_id == 0 || Auth::guard('admin')->user()->role_id == 17){
                                    $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                                    $s = $data->status == 1 ? 'selected' : '';
                                    $ns = $data->status == 0 ? 'selected' : '';
                                    return '<div class="action-list"><select class="process select droplinks '.$class.'"><option data-val="1" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>Activated</option><<option data-val="0" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>Deactivated</option>/select></div>';
                                }
                                $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                                $s = $data->status == 1 ? 'selected' : '';
                                $ns = $data->status == 0 ? 'selected' : '';
                                return '<div class="action-list"><select class="process select droplinks '.$class.'"><option disabled data-val="1" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>Activated</option><option  disabled data-val="0" value="'. route('admin-prod-status',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>Deactivated</option>/select></div>';

                            })
                            ->addColumn('action', function(Product $data) {
                                if(Auth::guard('admin')->user()->role_id == 0 || Auth::guard('admin')->user()->role_id == 17){
                                    return '<div class="godropdown"><button class="go-dropdown-toggle"> Actions<i class="fas fa-chevron-down"></i></button><div class="action-list"><a href="' . route('admin-prod-edit',$data->id) . '"> <i class="fas fa-edit"></i> Edit</a><a href="javascript" class="set-gallery" data-toggle="modal" data-target="#setgallery"><input type="hidden" value="'.$data->id.'"><i class="fas fa-eye"></i> View Gallery</a><a data-href="' . route('admin-prod-feature',$data->id) . '" class="feature" data-toggle="modal" data-target="#modal2"> <i class="fas fa-star"></i> Highlight</a><a href="javascript:;" data-href="' . route('admin-prod-catalog',['id1' => $data->id, 'id2' => 0]) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i> Remove Catalog</a></div></div>';
                                }
                            })
                            ->rawColumns(['name', 'status', 'action'])
                            ->toJson(); //--- Returning Json Data To Client Side
    }

    //*** GET Request
    public function index()
    {
        $stores = User::select('id', 'shop_name', 'name')->where('is_vendor','<>', 0)->get();
        return view('admin.product.index', compact('stores'));
    }

    // POST
    public function export(Request $request)
    {
        $store_id = ($request->has('store_id') && $request->store_id !== 'all') ? (int)$request->store_id : null;
        $status_product_id = ($request->has('status_product_id') && $request->status_product_id !== 'all')
            ? (int)$request->status_product_id : null;

        return Excel::download(new AdminProductsExport($store_id, $status_product_id), 'products.xlsx');
    }

    //*** GET Request
    public function indexReport()
    {
        return view('admin.product.report');
    }

    //*** GET Request
    public function indexReportModerator()
    {
        return view('admin.product.report-moderator');
    }
 
 

      //*** GET Request
    public function active()
    {
        return view('admin.product.active');
    }

     //*** GET Request
     public function waiting()
     {
         return view('admin.product.waiting');
     }

     //*** GET Request
    public function change()
    {
        return view('admin.product.change');
    }

    //*** GET Request
    public function deactive()
    {
        return view('admin.product.deactive');
    }

     //*** GET Request
    public function discount()
    {
        return view('admin.product.discount');
    }
    //*** GET Request
    public function featured()
    {
        return view('admin.product.featured');
    }
    
    //*** GET Request
    public function catalogs()
    {
        return view('admin.product.catalog');
    }

    //*** GET Request
    public function types()
    {
        return view('admin.product.types');
    }

    //*** GET Request
    public function createPhysical()
    {
        $cats = Category::all();
        $sign = Currency::where('is_default','=',1)->first();
        return view('admin.product.create.physical',compact('cats','sign'));
    }

    //*** GET Request
    public function createDigital()
    {
        $cats = Category::all();
        $sign = Currency::where('is_default','=',1)->first();
        return view('admin.product.create.digital',compact('cats','sign'));
    }

    //*** GET Request
    public function createLicense()
    {
        $cats = Category::all();
        $sign = Currency::where('is_default','=',1)->first();
        return view('admin.product.create.license',compact('cats','sign'));
    }

    //*** GET Request
    public function status($id1,$id2)
    {
        $data = Product::findOrFail($id1);
        $data->status = $id2;
        $data->issue_deactivate='';
        $data->changed_by = Auth::guard('admin')->user()->email;
        $data->update();
        // $find_prod = Product::findOrFail($id1);
        // $find_prod->changed_by = Auth::guard('admin')->user()->email;
        // $find_prod->save();
    }

    //*** POST  Request
    public function status_deactivate($id1,$id2)
    {

        $data = Product::findOrFail($id1);
        $data->status = $id2;
        $data->issue_deactivate=request()->issue_deactivate;
        $data->changed_by = Auth::guard('admin')->user()->email;
        $data->update();
    }

    //*** GET Request
    public function catalog($id1,$id2)
    {
        $data = Product::findOrFail($id1);
        $data->is_catalog = $id2;
        $data->update();
        if($id2 == 1) {
            $msg = "Product added to catalog successfully.";
        }
        else {
            $msg = "Product removed from catalog successfully.";
        }

        return response()->json($msg);

    }

    //*** POST Request
    public function uploadUpdate(Request $request,$id)
    {
        //--- Validation Section
        $rules = [
          'image' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
          return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        $data = Product::findOrFail($id);

        //--- Validation Section Ends
        // change start

        $image = $request->image;
        list($type, $image) = explode(';', $image);
        list(, $image)      = explode(',', $image);
        $image = base64_decode($image);
        $image_name = time().str_random(8).'.png';
        $path = '/assets/images/products/'.$image_name;
        Image::canvas(1024, 1024)
            ->fill('#ffffff')
            ->insert(Image::make($image)->resize(1024, 1024,
                function($constraint) {$constraint->aspectRatio();}), 'center')
            ->insert(public_path() . '/assets/images/watermark/watermark.png')
            ->save(public_path() . $path);

        // change end

                if($data->photo != null)
                {
                    if (file_exists(public_path().'/assets/images/products/'.$data->photo)) {
                        unlink(public_path().'/assets/images/products/'.$data->photo);
                    }
                }
                        $input['photo'] = $image_name;
         $data->update($input);
                if($data->thumbnail != null)
                {
                    if (file_exists(public_path().'/assets/images/thumbnails/'.$data->thumbnail)) {
                        unlink(public_path().'/assets/images/thumbnails/'.$data->thumbnail);
                    }
                }

        $img = Image::make(public_path().'/assets/images/products/'.$data->photo)->resize(285, 285);
        $thumbnail = time().str_random(8).'.jpg';
        $img->save(public_path().'/assets/images/thumbnails/'.$thumbnail);
        $data->thumbnail  = $thumbnail;
        $data->update();
        return response()->json(['status'=>true,'file_name' => $image_name]);
    }

    //*** POST Request
    //*** POST Request
    public function store(Request $request)
    {
        //--- Validation Section
        $rules = [
            // 'photo'      => 'required',
            'file'       => 'mimes:zip'
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        //--- Logic Section
        $data = new Product;
        $sign = Currency::where('is_default','=',1)->first();
        $input = $request->all();

        // Check File
        if ($file = $request->file('file')) {
            $name = time().$file->getClientOriginalName();
            $file->move('assets/files',$name);
            $input['file'] = $name;
        }

        if (!is_null($request->photo))
        {
            // change start

            $image = $request->photo;
            list($type, $image) = explode(';', $image);
            list(, $image)      = explode(',', $image);
            $image = base64_decode($image);
            $image_name = time().str_random(8).'.png';
            $path = '/assets/images/products/'.$image_name;
            Image::canvas(1024, 1024)
                ->fill('#ffffff')
                ->insert(Image::make($image)->resize(1024, 1024,
                    function($constraint) {$constraint->aspectRatio();}), 'center')
                ->insert(public_path() . '/assets/images/watermark/watermark.png')
                ->save(public_path() . $path);
            $input['photo'] = $image_name;

            // change end
        }

        // Check Physical
        if($request->type == "Physical")
        {

            //--- Validation Section
            $rules = ['sku'      => 'min:8|unique:products'];

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails()) {
                return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
            }
            //--- Validation Section Ends


            // Check Condition
            if ($request->product_condition_check == ""){
                $input['product_condition'] = 0;
            }

            // Check Shipping Time
            if ($request->shipping_time_check == ""){
                $input['ship'] = null;
            }

            // Check Size
            if(empty($request->size_check ))
            {
                $input['size'] = null;
                $input['size_qty'] = null;
                $input['size_price'] = null;
            }
            else{
                if(in_array(null, $request->size) || in_array(null, $request->size_qty))
                {
                    $input['size'] = null;
                    $input['size_qty'] = null;
                    $input['size_price'] = null;
                }
                else
                {
                    $input['size'] = implode(',', $request->size);
                    $input['size_qty'] = implode(',', $request->size_qty);
                    $input['size_price'] = implode(',', $request->size_price);
                }
            }


            // Check Whole Sale
            if(empty($request->whole_check ))
            {
                $input['whole_sell_qty'] = null;
                $input['whole_sell_discount'] = null;
            }
            else{
                if(in_array(null, $request->whole_sell_qty) || in_array(null, $request->whole_sell_discount))
                {
                $input['whole_sell_qty'] = null;
                $input['whole_sell_discount'] = null;
                }
                else
                {
                    $input['whole_sell_qty'] = implode(',', $request->whole_sell_qty);
                    $input['whole_sell_discount'] = implode(',', $request->whole_sell_discount);
                }
            }

            // Check Color
            if(empty($request->color_check))
            {
                $input['color'] = null;
            }
            else{
                $input['color'] = implode(',', $request->color);
            }

            // Check Measurement
            if ($request->mesasure_check == "")
            {
                $input['measure'] = null;
            }

        }

        // Check Seo
        if (empty($request->seo_check))
        {
            $input['meta_tag'] = null;
            $input['meta_description'] = null;
        }
        else {
            if (!empty($request->meta_tag))
            {
                $input['meta_tag'] = implode(',', $request->meta_tag);
            }
        }

        // Check License

        if($request->type == "License")
        {

            if(in_array(null, $request->license) || in_array(null, $request->license_qty))
            {
                $input['license'] = null;
                $input['license_qty'] = null;
            }
            else
            {
                $input['license'] = implode(',,', $request->license);
                $input['license_qty'] = implode(',', $request->license_qty);
            }

        }

        // Check Features
        if(in_array(null, $request->features) || in_array(null, $request->colors))
        {
            $input['features'] = null;
            $input['colors'] = null;
        }
        else
        {
            $input['features'] = implode(',', str_replace(',',' ',$request->features));
            $input['colors'] = implode(',', str_replace(',',' ',$request->colors));
        }

        //tags
        if (!empty($request->tags))
        {
            $input['tags'] = implode(',', $request->tags);
        }



        // Conert Price According to Currency
        $input['price'] = ($input['price'] / $sign->value);
        $input['previous_price'] = ($input['previous_price'] / $sign->value);



        // store filtering attributes for physical product
        $attrArr = [];
        if (!empty($request->category_id)) {
          $catAttrs = Attribute::where('attributable_id', $request->category_id)->where('attributable_type', 'App\Models\Category')->get();
          if (!empty($catAttrs)) {
            foreach ($catAttrs as $key => $catAttr) {
              $in_name = $catAttr->input_name;
              if ($request->has("$in_name")) {
                $attrArr["$in_name"]["values"] = $request["$in_name"];
                $attrArr["$in_name"]["prices"] = $request["$in_name"."_price"];
                if ($catAttr->details_status) {
                  $attrArr["$in_name"]["details_status"] = 1;
                } else {
                  $attrArr["$in_name"]["details_status"] = 0;
                }
              }
            }
          }
        }

        if (!empty($request->subcategory_id)) {
          $subAttrs = Attribute::where('attributable_id', $request->subcategory_id)->where('attributable_type', 'App\Models\Subcategory')->get();
          if (!empty($subAttrs)) {
            foreach ($subAttrs as $key => $subAttr) {
              $in_name = $subAttr->input_name;
              if ($request->has("$in_name")) {
                $attrArr["$in_name"]["values"] = $request["$in_name"];
                $attrArr["$in_name"]["prices"] = $request["$in_name"."_price"];
                if ($subAttr->details_status) {
                  $attrArr["$in_name"]["details_status"] = 1;
                } else {
                  $attrArr["$in_name"]["details_status"] = 0;
                }
              }
            }
          }
        }
        if (!empty($request->childcategory_id)) {
          $childAttrs = Attribute::where('attributable_id', $request->childcategory_id)->where('attributable_type', 'App\Models\Childcategory')->get();
          if (!empty($childAttrs)) {
            foreach ($childAttrs as $key => $childAttr) {
              $in_name = $childAttr->input_name;
              if ($request->has("$in_name")) {
                $attrArr["$in_name"]["values"] = $request["$in_name"];
                $attrArr["$in_name"]["prices"] = $request["$in_name"."_price"];
                if ($childAttr->details_status) {
                  $attrArr["$in_name"]["details_status"] = 1;
                } else {
                  $attrArr["$in_name"]["details_status"] = 0;
                }
              }
            }
          }
        }



        if (empty($attrArr)) {
          $input['attributes'] = NULL;
        } else {
          $jsonAttr = json_encode($attrArr);
          $input['attributes'] = $jsonAttr;
        }


        $data['status'] = 0; 
        // Save Data
        $data['created_by']=  Auth::guard('admin')->user()->email;
        
        $data->fill($input)->save();

        // Set SLug
        $prod = Product::find($data->id);
        if($prod->type != 'Physical'){
            $prod->slug = str_slug($data->name,'-').'-'.strtolower(str_random(3).$data->id.str_random(3));
        }
        else {
            $prod->slug = str_slug($data->name,'-').'-'.strtolower($data->sku);
        }

        // Set Thumbnail
        if (!is_null($request->photo))
        {
            $img = Image::make(public_path().'/assets/images/products/'.$prod->photo)->resize(285, 285);
            $thumbnail = time().str_random(8).'.jpg';
            $img->save(public_path().'/assets/images/thumbnails/'.$thumbnail);
            $prod->thumbnail  = $thumbnail;
            $prod->update();
        }

        // Add To Gallery If any
        $lastid = $data->id;
        if ($files = $request->file('gallery')){
            foreach ($files as  $key => $file){
                if(in_array($key, $request->galval))
                {
                    // change start

                    $gallery = new Gallery;
                    $name = time().$file->getClientOriginalName();
                    Image::canvas(1024, 1024)
                        ->fill('#ffffff')
                        ->insert(Image::make($file->getRealPath())->resize(1024, 1024,
                            function($constraint) {$constraint->aspectRatio();}), 'center')
                        ->insert(public_path() . '/assets/images/watermark/watermark.png')
                        ->save(public_path() . '/assets/images/galleries/' . $name);

                    $gallery['photo'] = $name;
                    $gallery['product_id'] = $lastid;
                    $gallery->save();

                    // change end
                }
            }
        }
        //logic Section Ends

        //--- Redirect Section
        $msg = 'New Product Added Successfully.<a href="'.route('admin-prod-index').'">View Product Lists.</a>';
        return response()->json($msg);
        //--- Redirect Section Ends
    }

    //*** POST Request
    public function import(){

        $cats = Category::all();
        $sign = Currency::where('is_default','=',1)->first();
        return view('admin.product.productcsv',compact('cats','sign'));
    }

    public function importSubmit(Request $request)
    {
        $log = "";
        //--- Validation Section
        $rules = [
            'csvfile'      => 'required|mimes:csv,txt',
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        $filename = '';
        if ($file = $request->file('csvfile'))
        {
            $filename = time().'-'.$file->getClientOriginalName();
            $file->move('assets/temp_files',$filename);
        }

        //$filename = $request->file('csvfile')->getClientOriginalName();
        //return response()->json($filename);
        $datas = "";
        
        $file = fopen(public_path('assets/temp_files/'.$filename),"r");
        $i = 1;
        $err = 1;
        while (($line = fgetcsv($file,false,";")) !== FALSE) {
           
            if($i != 1)
            {
                $line[1]= uniqid();
                $line[8]= str_replace(',','.',$line[8]);
               
                    // if( $line[15]!='' && $line[18]!=''){
                    if(!Product::where('barcode',$line[0])->exists() || $line[0]=='' ){

                            if (!Product::where('sku',$line[1])->exists()){
                            
                                            //--- Validation Section Ends

                                            //--- Logic Section
                                            $data = new Product;
                                            $sign = Currency::where('is_default','=',1)->first();

                                            $input['type'] = 'Physical';
                                            $input['barcode'] = $line[0];
                                            $input['sku'] = $line[1];
                                            $input['category_id'] = "";
                                            $input['subcategory_id'] = "";
                                            $input['childcategory_id'] = "";
                                            
                                            $mcat = Category::where(DB::raw('lower(name)'), strtolower($line[2]));
                                            //$mcat = Category::where("name", $line[1]);
                                        
                                            if($mcat->exists()){
                                                $input['category_id'] = $mcat->first()->id;

                                                if($line[3] != ""){
                                                    $scat = Subcategory::where(DB::raw('lower(name)'), strtolower($line[3]));

                                                    if($scat->exists()) {
                                                        $input['subcategory_id'] = $scat->first()->id;
                                                    }
                                                }
                                                if($line[4] != ""){
                                                    $chcat = Childcategory::where(DB::raw('lower(name)'), strtolower($line[4]));

                                                    if($chcat->exists()) {
                                                        $input['childcategory_id'] = $chcat->first()->id;
                                                    }
                                                }

                                            if($line[6] != ""){
                                                $img_product = Image::make($line[6]);
                                                $image_name = time().str_random(8).'.png';
                                                $img_product->save(public_path().'/assets/images/products/'.$image_name);

                                                $input['photo'] = $image_name;
                                            }
                                            $data['status'] = 0;
                                            $data['product_type'] = 'normal';
                                            $data['created_by'] = Auth::guard('admin')->user()->email;
                                            $input['name'] = $line[5];
                                            $input['details'] = $line[7];
                                        //$input['category_id'] = $request->category_id;
                                        //$input['subcategory_id'] = $request->subcategory_id;
                                        //$input['childcategory_id'] = $request->childcategory_id;
                                            $input['color'] = $line[14];
                                            $input['price'] = $line[8];
                                            $input['previous_price'] = $line[9];
                                            $input['stock'] = $line[10];
                                            $input['size'] = $line[11];
                                            $input['size_qty'] = $line[12];
                                            $input['size_price'] = $line[13];
                                            $input['youtube'] = $line[16];
                                            $input['policy'] = $line[17];
                                            $input['meta_tag'] = $line[18];
                                            $input['meta_description'] = $line[19];
                                            $input['tags'] = $line[15];
                                            $input['product_type'] = $line[20];
                                            $input['affiliate_link'] = $line[21];



                                            // Conert Price According to Currency
                                        //$input['price'] = ($input['price'] / $sign->value);
                                            //$input['previous_price'] = ($input['previous_price'] / $sign->value);

                                            // Save Data
                                            $data->fill($input)->save();
                                        
                                            // Set SLug
                                            $prod = Product::find($data->id);
                                                
                                            $prod->slug = str_slug($data->name,'-').'-'.strtolower($data->sku);

                                            // Set Thumbnail

                                            if($line[6] != ""){
                                                $img = Image::make($line[6])->resize(285, 285);
                                                $thumbnail = time().str_random(8).'.jpg';
                                                $img->save(public_path().'/assets/images/thumbnails/'.$thumbnail);
                                                $prod->thumbnail  = $thumbnail;
                                            }
                                            $prod->update();
                                            
                                        

                                            }else{
                                                $err = 2;
                                                $log .= "<br>Row No: ".$i."  - $line[2] category not found!<br>";
                                            }

                            }else{
                                $err = 2;
                                $log .= "<br>Row No: ".$i." - Duplicate Product Code: $line[1] <br>";  
                            }

                        // }else{
                        //     $err = 2;
                        //     $log .= "<br>Row No: ".$i." - Tags and Meta tags fileds is required <br>"; 
                        // }  
                     }else{
                        $err = 2;
                        $log .= "<br>Row No: ".$i." - Duplicate Barcode: $line[0] <br>";  
                    }   
            }

            $i++;

        }
        //dd();
        fclose($file);


        //--- Redirect Section
        $msg = 'Bulk Product File Imported Successfully.<a href="'.route('admin-prod-index').'">View Product Lists.</a>';
        if ($err==1) return response()->json($msg);
        elseif($err==2)return response()->json(array('errors' => [ 0 => $log ]));
    }


    //*** GET Request
    public function edit($id)
    {
        $cats = Category::all();
        $data = Product::findOrFail($id);
        $sign = Currency::where('is_default','=',1)->first();


        if($data->type == 'Digital')
            return view('admin.product.edit.digital',compact('cats','data','sign'));
        elseif($data->type == 'License')
            return view('admin.product.edit.license',compact('cats','data','sign'));
        else
            return view('admin.product.edit.physical',compact('cats','data','sign'));
    }

    //*** POST Request
    public function update(Request $request, $id)
    {
      // return $request;
        //--- Validation Section
        $rules = [
               'file'       => 'mimes:zip'
                ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
          return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends


        //-- Logic Section
        $data = Product::findOrFail($id);
        $sign = Currency::where('is_default','=',1)->first();
       
        $input = $request->all();

            //Check Types
            if($request->type_check == 1)
            {
                $input['link'] = null;
            }
            else
            {
                if($data->file!=null){
                        if (file_exists(public_path().'/assets/files/'.$data->file)) {
                        unlink(public_path().'/assets/files/'.$data->file);
                    }
                }
                $input['file'] = null;
            }


            // Check Physical
            if($data->type == "Physical")
            {

                    //--- Validation Section
                    $rules = ['sku' => 'min:8|unique:products,sku,'.$id];

                    $validator = Validator::make(Input::all(), $rules);

                    if ($validator->fails()) {
                        return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
                    }
                    //--- Validation Section Ends

                        // Check Condition
                        if ($request->product_condition_check == ""){
                            $input['product_condition'] = 0;
                        }

                        // Check Shipping Time
                        if ($request->shipping_time_check == ""){
                            $input['ship'] = null;
                        }

                        // Check Size

                        if(empty($request->size_check ))
                        {
                            $input['size'] = null;
                            $input['size_qty'] = null;
                            $input['size_price'] = null;
                        }
                        else{
                                if(in_array(null, $request->size) || in_array(null, $request->size_qty) || in_array(null, $request->size_price))
                                {
                                    $input['size'] = null;
                                    $input['size_qty'] = null;
                                    $input['size_price'] = null;
                                }
                                else
                                {
                                    $input['size'] = implode(',', $request->size);
                                    $input['size_qty'] = implode(',', $request->size_qty);
                                    $input['size_price'] = implode(',', $request->size_price);
                                }
                        }



                        // Check Whole Sale
            if(empty($request->whole_check ))
            {
                $input['whole_sell_qty'] = null;
                $input['whole_sell_discount'] = null;
            }
            else{
                if(in_array(null, $request->whole_sell_qty) || in_array(null, $request->whole_sell_discount))
                {
                $input['whole_sell_qty'] = null;
                $input['whole_sell_discount'] = null;
                }
                else
                {
                    $input['whole_sell_qty'] = implode(',', $request->whole_sell_qty);
                    $input['whole_sell_discount'] = implode(',', $request->whole_sell_discount);
                }
            }

                        // Check Color
                        if(empty($request->color_check ))
                        {
                            $input['color'] = null;
                        }
                        else{
                            if (!empty($request->color))
                             {
                                $input['color'] = implode(',', $request->color);
                             }
                            if (empty($request->color))
                             {
                                $input['color'] = null;
                             }
                        }

                        // Check Measure
                    if ($request->measure_check == "")
                     {
                        $input['measure'] = null;
                     }
            }


            // Check Seo
        if (empty($request->seo_check))
         {
            $input['meta_tag'] = null;
            $input['meta_description'] = null;
         }
         else {
        if (!empty($request->meta_tag))
         {
            $input['meta_tag'] = implode(',', $request->meta_tag);
         }
         }



        // Check License
        if($data->type == "License")
        {

        if(!in_array(null, $request->license) && !in_array(null, $request->license_qty))
        {
            $input['license'] = implode(',,', $request->license);
            $input['license_qty'] = implode(',', $request->license_qty);
        }
        else
        {
            if(in_array(null, $request->license) || in_array(null, $request->license_qty))
            {
                $input['license'] = null;
                $input['license_qty'] = null;
            }
            else
            {
                $license = explode(',,', $prod->license);
                $license_qty = explode(',', $prod->license_qty);
                $input['license'] = implode(',,', $license);
                $input['license_qty'] = implode(',', $license_qty);
            }
        }

        }
            // Check Features
            if(!in_array(null, $request->features) && !in_array(null, $request->colors))
            {
                    $input['features'] = implode(',', str_replace(',',' ',$request->features));
                    $input['colors'] = implode(',', str_replace(',',' ',$request->colors));
            }
            else
            {
                if(in_array(null, $request->features) || in_array(null, $request->colors))
                {
                    $input['features'] = null;
                    $input['colors'] = null;
                }
                else
                {
                    $features = explode(',', $data->features);
                    $colors = explode(',', $data->colors);
                    $input['features'] = implode(',', $features);
                    $input['colors'] = implode(',', $colors);
                }
            }

        //Product Tags
        if (!empty($request->tags))
         {
            $input['tags'] = implode(',', $request->tags);
         }
        if (empty($request->tags))
         {
            $input['tags'] = null;
         }


        //  $input['price'] = $input['price'] / $sign->value;
        //  $input['previous_price'] = $input['previous_price'] / $sign->value;
        $input['price'] = $input['price'];
        $input['previous_price'] = $input['previous_price'] ;

         // store filtering attributes for physical product
         $attrArr = [];
         if (!empty($request->category_id)) {
           $catAttrs = Attribute::where('attributable_id', $request->category_id)->where('attributable_type', 'App\Models\Category')->get();
           if (!empty($catAttrs)) {
             foreach ($catAttrs as $key => $catAttr) {
               $in_name = $catAttr->input_name;
               if ($request->has("$in_name")) {
                 $attrArr["$in_name"]["values"] = $request["$in_name"];
                 $attrArr["$in_name"]["prices"] = $request["$in_name"."_price"];
                 if ($catAttr->details_status) {
                   $attrArr["$in_name"]["details_status"] = 1;
                 } else {
                   $attrArr["$in_name"]["details_status"] = 0;
                 }
               }
             }
           }
         }

         if (!empty($request->subcategory_id)) {
           $subAttrs = Attribute::where('attributable_id', $request->subcategory_id)->where('attributable_type', 'App\Models\Subcategory')->get();
           if (!empty($subAttrs)) {
             foreach ($subAttrs as $key => $subAttr) {
               $in_name = $subAttr->input_name;
               if ($request->has("$in_name")) {
                 $attrArr["$in_name"]["values"] = $request["$in_name"];
                 $attrArr["$in_name"]["prices"] = $request["$in_name"."_price"];
                 if ($subAttr->details_status) {
                   $attrArr["$in_name"]["details_status"] = 1;
                 } else {
                   $attrArr["$in_name"]["details_status"] = 0;
                 }
               }
             }
           }
         }
         if (!empty($request->childcategory_id)) {
           $childAttrs = Attribute::where('attributable_id', $request->childcategory_id)->where('attributable_type', 'App\Models\Childcategory')->get();
           if (!empty($childAttrs)) {
             foreach ($childAttrs as $key => $childAttr) {
               $in_name = $childAttr->input_name;
               if ($request->has("$in_name")) {
                 $attrArr["$in_name"]["values"] = $request["$in_name"];
                 $attrArr["$in_name"]["prices"] = $request["$in_name"."_price"];
                 if ($childAttr->details_status) {
                   $attrArr["$in_name"]["details_status"] = 1;
                 } else {
                   $attrArr["$in_name"]["details_status"] = 0;
                 }
               }
             }
           }
         }



         if (empty($attrArr)) {
           $input['attributes'] = NULL;
         } else {
           $jsonAttr = json_encode($attrArr);
           $input['attributes'] = $jsonAttr;
         }


            if($data->type != 'Physical'){
                $data->slug = str_slug($data->name,'-').'-'.strtolower(str_random(3).$data->id.str_random(3));
            }
            else {
                $data->slug = str_slug($data->name,'-').'-'.strtolower($data->sku);
            }

       
         $input['changed_by'] =   Auth::guard('admin')->user()->email;
         $input['status'] = 3;
         $data->update($input);
        //-- Logic Section Ends

        //--- Redirect Section
        $msg = 'Product Updated Successfully.<a href="'.route('admin-prod-index').'">View Product Lists.</a>';
        return response()->json($msg);
        //--- Redirect Section Ends
    }


    //*** GET Request
    public function feature($id)
    {
            $data = Product::findOrFail($id);
            return view('admin.product.highlight',compact('data'));
    }

    //*** POST Request
    public function featuresubmit(Request $request, $id)
    {
        //-- Logic Section
            $data = Product::findOrFail($id);
            $input = $request->all();
            if($request->featured == "")
            {
                $input['featured'] = 0;
            }
            if($request->hot == "")
            {
                $input['hot'] = 0;
            }
            if($request->best == "")
            {
                $input['best'] = 0;
            }
            if($request->top == "")
            {
                $input['top'] = 0;
            }
            if($request->latest == "")
            {
                $input['latest'] = 0;
            }
            if($request->big == "")
            {
                $input['big'] = 0;
            }
            if($request->trending == "")
            {
                $input['trending'] = 0;
            }
            if($request->sale == "")
            {
                $input['sale'] = 0;
            }
            if($request->is_discount == "")
            {
                $input['is_discount'] = 0;
                $input['discount_date'] = null;
            }

            $data->update($input);
        //-- Logic Section Ends

        //--- Redirect Section
        $msg = 'Highlight Updated Successfully.';
        return response()->json($msg);
        //--- Redirect Section Ends

    }

    //*** GET Request
    public function destroy($id)
    {

        $data = Product::findOrFail($id);
        if($data->galleries->count() > 0)
        {
            foreach ($data->galleries as $gal) {
                    if (file_exists(public_path().'/assets/images/galleries/'.$gal->photo)) {
                        unlink(public_path().'/assets/images/galleries/'.$gal->photo);
                    }
                $gal->delete();
            }

        }

        if($data->reports->count() > 0)
        {
            foreach ($data->reports as $gal) {
                $gal->delete();
            }
        }

        if($data->ratings->count() > 0)
        {
            foreach ($data->ratings  as $gal) {
                $gal->delete();
            }
        }
        if($data->wishlists->count() > 0)
        {
            foreach ($data->wishlists as $gal) {
                $gal->delete();
            }
        }
        if($data->clicks->count() > 0)
        {
            foreach ($data->clicks as $gal) {
                $gal->delete();
            }
        }
        if($data->comments->count() > 0)
        {
            foreach ($data->comments as $gal) {
            if($gal->replies->count() > 0)
            {
                foreach ($gal->replies as $key) {
                    $key->delete();
                }
            }
                $gal->delete();
            }
        }

        if($data->photo!=''){
            if (!filter_var($data->photo,FILTER_VALIDATE_URL)){
                if (file_exists(public_path().'/assets/images/products/'.$data->photo)) {
                    unlink(public_path().'/assets/images/products/'.$data->photo);
                }
            }
        }
        if (file_exists(public_path().'/assets/images/thumbnails/'.$data->thumbnail) && $data->thumbnail != "") {
            unlink(public_path().'/assets/images/thumbnails/'.$data->thumbnail);
        }

        if($data->file != null){
            if (file_exists(public_path().'/assets/files/'.$data->file)) {
                unlink(public_path().'/assets/files/'.$data->file);
            }
        }
        $data->delete();
        //--- Redirect Section
        $msg = 'Product Deleted Successfully.';
        return response()->json($msg);
        //--- Redirect Section Ends

// PRODUCT DELETE ENDS
    }

    public function getAttributes(Request $request) {
      $model = '';
      if ($request->type == 'category') {
        $model = 'App\Models\Category';
      } elseif ($request->type == 'subcategory') {
        $model = 'App\Models\Subcategory';
      } elseif ($request->type == 'childcategory') {
        $model = 'App\Models\Childcategory';
      }

      $attributes = Attribute::where('attributable_id', $request->id)->where('attributable_type', $model)->get();
      $attrOptions = [];
      foreach ($attributes as $key => $attribute) {
        $options = AttributeOption::where('attribute_id', $attribute->id)->get();
        $attrOptions[] = ['attribute' => $attribute, 'options' => $options];
      }
      return response()->json($attrOptions);
    }
}
