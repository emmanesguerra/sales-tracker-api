<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class SalesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $time = new \DateTime($this->order_time);
        return [
            'order_date' => $this->order_date,
            'order_time' => $time->format('H:i'),
            'item_name' => $this->item ? $this->item->name : null,
            'item_price' => $this->item_price,
            'quantity' => $this->quantity,
            'total_amount' => $this->total_amount,
        ];
    }
}
