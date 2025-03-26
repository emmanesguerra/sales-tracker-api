<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order_date' => $this->order_date,
            'order_time' => $this->order_time,
            'item_name' => $this->item ? $this->item->name : null,
            'item_price' => $this->item_price,
            'quantity' => $this->quantity,
            'total_amount' => $this->total_amount,
        ];
    }
}
