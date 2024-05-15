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
            'Game Number' => $this->id,
            'Dice 1' => $this->dice1,
            'Dice 2' => $this->dice2,
            'Result' => $resultString,
        ];
    }
}
