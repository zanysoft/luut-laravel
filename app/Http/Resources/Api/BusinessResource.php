<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type_id' => $this->type_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'phone' => $this->phone,
            //'phone2' => $this->phone2,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'website' => $this->website,

            'address' => $this->address,
            'address2' => $this->address2,
            'city' => $this->city,
            'state' => $this->state,
            'country_code' => $this->country_code,
            'zipcode' => $this->whenHas('zipcode'),
            'time_zone' => $this->whenHas('time_zone'),
            'language' => $this->whenHas('language'),
            'social_links' => $this->whenHas('social_links'),
        ];
    }
}
