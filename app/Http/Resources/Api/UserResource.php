<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'value' => $this->id,
            'label' => $this->name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => $this->whenHas('name'),
            'email' => $this->email,
            'username' => $this->whenHas('username'),
            'about' => $this->whenHas('about'),
            'ip_address' => $this->whenHas('ip_address'),
            'gender' => $this->whenHas('gender'),
            'phone' => $this->whenHas('phone'),
            'dob' => $this->whenHas('dob'),
            'address' => $this->whenHas('address'),
            'age' => $this->whenHas('age'),
            'language' => $this->whenHas('language'),
            'type' => $this->whenHas('user_type'),

            'token' => $this->whenHas('token'),
            'created_at' => $this->whenHas('created_at'),
            'status' => $this->whenHas('status'),
            'new' => $this->whenHas('new'),
            'business' => BusinessResource::make($this->business),
        ];
    }
}
