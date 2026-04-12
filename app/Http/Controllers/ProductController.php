<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
    */
    use AuthorizesRequests;

   /*  public function __construct()
    {
        $this->authorizeResource(Product::class, 'product');


    } */

    public function index()
    {
        
        $products = Product::paginate(10);
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        Gate::authorize('create', Product::class);  

        $product = Product::create([
            ...$request->validate([
                'name'=>'required|string|max:255|',
                'description'=>'required|string|max:255',
                'price'=>'required',
                'quantity'=>'required',
                'category_id'=>'required',
                'discount_price' => 'sometimes|integer',
                "weight" => 'sometimes|integer'
            ])
        ]);
        $product->load('category');
        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $product;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        Gate::authorize('update',$product);   

        $data = $request->validate([
            "name"=>"sometimes|string",
            "price"=>"sometimes|integer",
            "description"=>"sometimes|string",
            "stock"=>"sometimes|integer",
            "is_active"=>"sometimes|boolean",
            "category_id"=>"sometimes|integer"

        ]);

        $product->update($data);

        return response()->json([
            'message' => "product updated",
            'product' =>$product
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        Gate::authorize('delete',$product);   


        $product->delete();

        return response()->json([
            'message'=> "A termék törlődött"
        ]);
    }
}
