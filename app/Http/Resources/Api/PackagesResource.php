<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class PackagesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'amount' => $this->amount,
            'setup_fee' => $this->setup_fee,
            'description' => $this->description,
            'type' => $this->package_type,
            'note' => $this->payment_note,
            'picture' => $this->picture,
        ];
    }
}
