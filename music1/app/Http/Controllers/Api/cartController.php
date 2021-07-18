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
        if (Cart::where('ip', $ip)->where('submited', false)->first() === null)
        {
            $new_cart = Cart::create([
                'ip' => $ip,
                'submited' => false
            ]);
        }
        return new cartResource(Cart::where('ip', $ip)->where('submited', false)->first());
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
     * @return ProductsListResource|PoductsListResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        $this->index($request);
        $user_id = Cart::where('ip', $request->ip())->where('submited', false)->first()->id;
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
            $product_in_list = ProductsList::where('product_id', $request->id)->where('cart_id', $user_id)->first();
            return new PoductsListResource($product_in_list);
        }
        return response(['error' => 'no such product'], Response::HTTP_BAD_REQUEST);
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
     * @return PoductsListResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if (!is_int($request->count) || !is_int($request->id))
        {
            return response(['error' => 'wrong keys or values'], Response::HTTP_BAD_REQUEST);
        }
        if ($request->count < 1)
        {
            return response(['error' => 'products amount must be >1'], Response::HTTP_BAD_REQUEST);
        }
        $this->index($request);
        $user_id = Cart::where('ip', $request->ip())->where('submited', false)->first()->id;
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
            $product_in_list = ProductsList::where('product_id', $request->id)->where('cart_id', $user_id)->first();
            return new PoductsListResource($product_in_list);
        }
        return response(['error' => 'no such product'], Response::HTTP_BAD_REQUEST);
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
        $user_id = Cart::where('ip', $request->ip())->where('submited', false)->first()->id;
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
                return response(null, Response::HTTP_OK);
            }
            else {
                return response(['error' => 'no such product in cart'], Response::HTTP_BAD_REQUEST);
            }
        }
        return response(['error' => 'no such product'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return PoductsListResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function submit(Request $request)
    {
        $user = Cart::where('ip', $request->ip())->where('submited', false)->first();
        if ($user === null)
        {
            $this->index($request);
            return response(['error' => 'cart is empty'], Response::HTTP_BAD_REQUEST);
        }
        if (ProductsList::where('cart_id', $user->id)->first() === null)
        {
            return response(['error' => 'cart is empty'], Response::HTTP_BAD_REQUEST);
        }
        if (!is_string($request->email) || !is_string($request->phone))
        {
            return response(['error' => 'wrong key or value'], Response::HTTP_BAD_REQUEST);
        }
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return response(['error' => 'invalid email'], Response::HTTP_BAD_REQUEST);
        }
        if (!preg_match('#^[0-9]+\z#', $request->phone))
        {
            return response(['error' => 'invalid phone_number'], Response::HTTP_BAD_REQUEST);
        }
        $user->update([
            'email' => $request->email,
            'phone' => $request->phone,
            'submited' => true
        ]);
        return response(new cartResource($user), Response::HTTP_OK);
    }
}
