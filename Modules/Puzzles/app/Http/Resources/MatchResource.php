<?php

namespace Modules\Puzzles\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
            ],
            'matching_pieces' => $this->when(isset($this->matching_pieces), $this->matching_pieces ?? []),
            'match_count' => $this->when(isset($this->match_count), $this->match_count ?? 0),
        ];
    }
}
