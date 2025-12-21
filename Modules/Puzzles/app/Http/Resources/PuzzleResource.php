<?php

namespace Modules\Puzzles\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PuzzleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'album_id' => $this->puzzles_album_id,
            'name' => $this->name,
            'position' => $this->position,
            'image_url' => $this->getFirstMediaUrl('image'),
            'pieces_count' => $this->whenCounted('pieces'),
            'completed_pieces' => $this->when(isset($this->completed_pieces), $this->completed_pieces ?? 0),
            'completion_percentage' => $this->completed_pieces === 0 ?: $this->when(isset($this->pieces_count) && $this->pieces_count > 0,
                round(($this->completed_pieces ?? 0) / $this->pieces_count * 100)),
            'pieces' => PieceResource::collection($this->whenLoaded('pieces')),
        ];
    }
}
