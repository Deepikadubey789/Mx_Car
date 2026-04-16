<?php

namespace Botble\CarRentals\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClaimTimelineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $row = (array) $this->resource;

        return [
            'occurred_at' => $row['occurred_at'] ?? null,
            'category' => $row['category'] ?? null,
            'title' => $row['title'] ?? null,
            'summary' => $row['summary'] ?? null,
            'metadata' => $row['metadata'] ?? [],
            'actor' => $row['actor'] ?? null,
            'source' => $row['source'] ?? null,
        ];
    }
}
