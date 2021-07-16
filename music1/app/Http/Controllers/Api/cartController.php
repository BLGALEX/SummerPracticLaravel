<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\cartResource;
use App\Http\Resources\PoductsListResource;
use App\Http\Resources\productResource;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductsList;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;
use function GuzzleHttp\Promise\all;

class cartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return cartResource
     */
    public function index(Request $request)
    {
        $ip = $request->ip();
        if (Cart::where('ip', $ip)->first() === null)
        {
            $new_cart = Cart::create([
                'ip' => $ip
            ]);
        }
        return new cartResource(Cart::where('ip', $ip)->first());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return ProductsListResource|PoductsListResource|\Illuminate\Http\JsonResponse
     */
    public function add(Request $request)
    {
        $this->index($request);
        $user_id = Cart::where('ip', $request->ip())->first()->id;
        if (Product::where('id', $request->id)->first() != null)
        {
            $product_in_list = ProductsList::where('product_id', $request->id)->where('cart_id', $user_id)->first();
            if ($product_in_list != null)
            {
                $count = 1+$product_in_list->count;
                $product_in_list->update([
                    'count' => $count
                ]);
            }
            else {
                ProductsList::create([
                    'cart_id' => $user_id,
                    'product_id' => $request->id,
                    'count' => 1
            ]);
            }
            return new PoductsListResource($product_in_list);
        }
        return response()->json(['error' => 'no such product'], 404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return PoductsListResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        if (!is_int($request->count) || !is_int($request->id))
        {
            return response()->json(['error' => 'wrong keys or values'], 400);
        }
        if ($request->count < 1)
        {
            return response()->json(['error' => 'products amount must be >1'], 400);
        }
        $this->index($request);
        $user_id = Cart::where('ip', $request->ip())->first()->id;
        if (Product::where('id', $request->id)->first() != null)
        {
            $product_in_list = ProductsList::where('product_id', $request->id)->where('cart_id', $user_id)->first();
            if ($product_in_list != null)
            {
                $count = 1+$product_in_list->count;
                $product_in_list->update([
                    'count' => $request->count
                ]);
            }
            else {
                ProductsList::create([
                    'cart_id' => $user_id,
                    'product_id' => $request->id,
                    'count' => $request->count
                ]);
            }
            return new PoductsListResource($product_in_list);
        }
        return response()->json(['error' => 'no such product'], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return PoductsListResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $this->index($request);
        $user_id = Cart::where('ip', $request->ip())->first()->id;
        if (!is_int($request->id))
        {
            return response()->json(['error' => 'wrong key or value'], 400);
        }

        if (Product::where('id', $request->id)->first() != null)
        {
            $product_in_list = ProductsList::where('product_id', $request->id)->where('cart_id', $user_id)->first();
            if ($product_in_list != null)
            {
                $product_in_list->delete();
                return response(null, Response::HTTP_NO_CONTENT);
            }
            else {
                return response(null, Response::HTTP_BAD_REQUEST);
            }
        }
        return response()->json(['error' => 'no such product'], 400);
    }
}
