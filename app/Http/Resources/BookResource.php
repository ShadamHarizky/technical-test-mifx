<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\BookContentResource;


class BookResource extends JsonResource
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
            'isbn' => $this->isbn,
            'title' => $this->title,
            'description' => $this->description,
            'published_year' => $this->published_year,
            'authors' => $this->whenLoaded('authors', AuthorResource::collection($this->authors)),
            'book_contents' => [],
            'price' => $this->price,
            'price_rupiah' => usd_to_rupiah_format($this->price),
            'review' => [
                'avg' => $this->reviews ? round($this->reviews()->avg('review')) : 0,
                'count' => $this->reviews ? $this->reviews()->count() : 0,
            ],
        ];
    }
}
