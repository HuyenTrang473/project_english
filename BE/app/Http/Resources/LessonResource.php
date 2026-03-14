<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->tieu_de,
            'description' => $this->mo_ta,
            'content' => $this->noi_dung,
            'status' => $this->trang_thai,
            'isPublished' => $this->when(method_exists($this->resource, 'isPublished'), fn() => $this->isPublished()),
            'teacher' => $this->whenLoaded('giaoVien', fn() => [
                'id' => $this->giaoVien->id,
                'name' => $this->giaoVien->name,
                'email' => $this->giaoVien->email,
            ]),
            'createdAt' => $this->created_at,
        ];
    }
}
