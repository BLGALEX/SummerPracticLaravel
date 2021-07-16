<?php

namespace App\Http\Resources;

use App\Models\Cart;
use App\Models\ProductsList;
use Illuminate\Http\Resources\Json\JsonResource;

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
        $list = PeoductsListResource::collection($products);
        if ($products == null)
        {
            error_log(1);
        }
        return [
            'id' => $this->id,
            'ip' => $this->ip,
            'e-mail' => $this->email,
            'phone' => $this->phone,
            'products' => $list
        ];

    }
}
