<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('product')->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $category = Category::create([
            ...$request->validate([
                'name'=>'required|string|max:255|',
                'description'=>'required|string|max:255'

            ])
        ]);

        return $category;
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
         Gate::authorize('update',$category);   
         $data = $request->validate([
            "name" => "sometimes|string",
            "description" => "sometimes|string"
         ]);

         $category->update($data);

          return response()->json([
            'message' => "Kategória frissítve",
            'product' =>$category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
         Gate::authorize('delete',$category);   


        $category->delete();

        return response()->json([
            'message'=> "A kategóra törlődött"
        ]);
    }

    public function viewCategories(Category $category) {
         $categories = Category::with('product')->get();
         return $categories;
    }
}
