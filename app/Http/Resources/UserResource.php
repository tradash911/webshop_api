<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" =>$this->id,
            "name" => $this->name,
            "email"=>$this->email,
            //"address"=>$this->address,
            "zip" => $this->zip,
            "city" => $this->city,
            "address_line" => $this->address_line,
            "phone" =>$this->phone,
            "newsletter_subscribed" => $this->newsletter_subscribed,
            "is_admin"=>$this->is_admin,
            "created_at" =>$this->created_at,
            "updated_at" =>$this->updated_at,
            "orders" => OrderResource::collection($this->whenLoaded('orders'))
            
        ];
    }
}
