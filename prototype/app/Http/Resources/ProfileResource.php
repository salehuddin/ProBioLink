<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Profile */
class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'username' => $this->username,
            'display_name' => $this->display_name,
            'headline' => $this->headline,
            'location' => $this->location,
            'avatar_url' => $this->avatar_url,
            'template' => $this->template,
            'preset' => $this->preset,
            'blocks' => BlockResource::collection($this->whenLoaded('blocks')),
        ];
    }
}
