<?php

namespace Lara\Jarvis\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DefaultCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
