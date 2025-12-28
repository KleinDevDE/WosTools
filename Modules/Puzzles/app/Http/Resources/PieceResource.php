<?php

namespace Modules\Puzzles\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PieceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'album_id' => $this->puzzles_album_id,
            'puzzle_id' => $this->puzzles_album_puzzle_id,
            'position' => $this->position,
            'stars' => $this->stars,
            'is_tradeable' => $this->isTradeable(),
            'image_url' => $this->getFirstMediaUrl('image'),
        ];
    }
}
