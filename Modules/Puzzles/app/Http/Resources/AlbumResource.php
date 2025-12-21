<?php

namespace Modules\Puzzles\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position,
            'cover_url' => $this->getFirstMediaUrl('cover'),
            'puzzles_count' => $this->whenCounted('puzzles'),
            'total_pieces' => $this->when(isset($this->total_pieces), $this->total_pieces ?? 0),
            'completed_pieces' => $this->when(isset($this->completed_pieces), $this->completed_pieces ?? 0),
            'completion_percentage' => $this->completed_pieces === 0 ?: $this->when(isset($this->total_pieces) && $this->total_pieces > 0,
                round(($this->completed_pieces ?? 0) / $this->total_pieces * 100)),
            'puzzles' => PuzzleResource::collection($this->whenLoaded('puzzles')),
        ];
    }
}
