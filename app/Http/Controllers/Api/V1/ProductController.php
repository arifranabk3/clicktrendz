<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource with strategic pagination.
     */
    public function index(Request $request)
    {
        $query = Product::where('is_active', true);

        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        return $query->paginate(24);
    }

    /**
     * Display the specified resource by Slug for Next.js ISR compatibility.
     */
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json($product);
    }
}
