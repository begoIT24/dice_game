<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {      
        //winGame boolean to string
        if ($this->winGame) {
            $resultString = "Game Won";
        } else $resultString = "Game Lost";

        return [
            'game_number' => $this->id,
            'dice1' => $this->dice1,
            'dice2' => $this->dice2,
            'result' => $resultString,
        ];
    }
}
