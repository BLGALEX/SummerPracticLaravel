<?php

namespace App\Http\Resources;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductsList;
use Brick\Math\BigInteger;
use Brick\Math\BigNumber;
use Illuminate\Http\Resources\Json\JsonResource;
use phpDocumentor\Reflection\Types\Integer;

class cartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $products = ProductsList::where('cart_id', $this->id)->get();
        $sum = (float) 0;
        foreach ($products as $product)
        {
            $product_instance = Product::find($product->product_id);
            $sum+=(float)$product->count*$product_instance->price;
        }
        $list = PoductsListResource::collection($products);
        if ($sum < 2147483647)
        {
            $sum = (int)$sum;
        }
        return [
            'id' => $this->id,
            'ip' => $this->ip,
            'e-mail' => $this->email,
            'phone' => $this->phone,
            'final_price' => $sum,
            'products' => $list
        ];

    }
}
