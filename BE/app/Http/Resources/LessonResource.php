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
            'statusText' => $this->trang_thai === 2 ? 'Đã xuất bản' : 'Nháp',
            'isPublished' => $this->isPublished(),
            'isDraft' => $this->isDraft(),
            'file' => $this->when($this->file_path, fn() => [
                'path' => $this->file_path,
                'type' => $this->file_type,
                'size' => $this->file_size,
                'url' => $this->file_path ? url('storage/' . $this->file_path) : null,
            ]),
            'teacher' => $this->whenLoaded('giaoVien', fn() => [
                'id' => $this->giaoVien->id,
                'name' => $this->giaoVien->name,
                'email' => $this->giaoVien->email,
            ]),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
