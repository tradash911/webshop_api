<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "user_id" => $this->user_id,
            "total_price" => $this->total_price,
            "order_number" => $this->order_number,
            "status" => $this->status,
    "shipping" => [
            "name" => $this->name,
            "zip" => $this->zip,
            "city" => $this->city,
            "address_line" => $this->address_line,
            "email" => $this->email,
            "phone" => $this->phone,
         ],

    "billing" => [
            "name" => $this->billing_name,
            "zip" => $this->billing_zip,
            "city" => $this->billing_city,
            "address_line" => $this->billing_address_line,
            "company_name" => $this->company_name,
            "tax_id" => $this->tax_id,
    ],
            "items" => OrderItemsResource::collection($this->whenLoaded('orderItems'))
        ];
    }
}
