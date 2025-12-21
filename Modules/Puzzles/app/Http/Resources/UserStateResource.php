<?php

namespace Modules\Puzzles\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserStateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'piece_id' => $this->puzzles_album_puzzle_piece_id,
            'state' => $this->state,
        ];
    }
}
