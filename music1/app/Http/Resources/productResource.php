<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;

class productResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->name,
            'image' => $this->picture,
            'price' => $this->price,
            'category' => Category::find($this->category_id)->name,
            'characteristics' => $this->characteristics
        ];
    }
}
