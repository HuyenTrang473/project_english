<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaiTestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'id_giao_vien' => $this->id_giao_vien,
            'id_lesson' => $this->id_lesson,
            'ten_bai_test' => $this->ten_bai_test,
            'loai_quiz' => $this->loai_quiz,
            'chi_tiet_loai_quiz' => $this->chi_tiet_loai_quiz,
            'mo_ta' => $this->mo_ta,
            'thoi_gian_toi_da' => $this->thoi_gian_toi_da,
            'diem_tong_max' => $this->diem_tong_max,
            'so_lan_lam_toi_da' => $this->so_lan_lam_toi_da,
            'co_xao_tron_cau_hoi' => $this->co_xao_tron_cau_hoi ?? false,
            'co_xao_tron_dap_an' => $this->co_xao_tron_dap_an ?? false,
            'hien_thi_ket_qua_ngay_lap' => $this->hien_thi_ket_qua_ngay_lap ?? true,
            'hien_thi_dap_an_dung' => $this->hien_thi_dap_an_dung ?? true,
            'cho_xem_lai_test' => $this->cho_xem_lai_test ?? true,
            'diem_dat' => $this->diem_dat,
            'trang_thai' => $this->trang_thai,
            'ngay_bat_dau' => $this->ngay_bat_dau,
            'ngay_ket_thuc' => $this->ngay_ket_thuc,
            'lesson' => $this->whenLoaded('lesson', fn() => [
                'id' => $this->lesson->id,
                'tieu_de' => $this->lesson->tieu_de,
                'mo_ta' => $this->lesson->mo_ta ?? '',
                'loai_bai_hoc' => $this->lesson->loai_bai_hoc ?? '',
            ]),
            'giaoVien' => $this->whenLoaded('giaoVien', fn() => [
                'id' => $this->giaoVien->id,
                'name' => $this->giaoVien->name,
            ]),
            'questions' => $this->whenLoaded('cauHois', fn() => $this->cauHois->map(function ($q) use ($request) {
                return [
                    'id' => $q->id,
                    'id_bai_test' => $q->id_bai_test,
                    'noi_dung' => $q->noi_dung,
                    'mo_ta_chi_tiet' => $q->mo_ta_chi_tiet,
                    'loai_cau_hoi' => $q->loai_cau_hoi,
                    'hinh_anh_url' => $q->hinh_anh_url,
                    'audio_url' => $this->normalizeAudioUrl($q->audio_url, $request),
                    'audio_file_name' => $q->audio_file_name,
                    'audio_file_size' => $q->audio_file_size,
                    'ghi_chu' => $q->ghi_chu,
                    'diem_max' => $q->diem_max,
                    'diem_toi_da' => $q->diem_max, // alias for frontend
                    'thu_tu' => $q->thu_tu,
                    'answers' => $q->relationLoaded('dapAns') ? $q->dapAns->map(function ($a) {
                        return [
                            'id' => $a->id,
                            'id_cau_hoi' => $a->id_cau_hoi,
                            'noi_dung' => $a->noi_dung,
                            'hinh_anh_url' => $a->hinh_anh_url,
                            'mo_ta_chi_tiet' => $a->mo_ta_chi_tiet,
                            'la_dap_an_dung' => $a->la_dap_an_dung,
                            'diem_tu_dong' => $a->diem_tu_dong,
                            'thu_tu' => $a->thu_tu,
                        ];
                    }) : [],
                ];
            })),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function normalizeAudioUrl($audioUrl, Request $request): ?string
    {
        if (!$audioUrl || !is_string($audioUrl)) {
            return null;
        }

        $audioUrl = trim($audioUrl);
        if ($audioUrl === '' || str_starts_with($audioUrl, 'blob:')) {
            return null;
        }

        $origin = rtrim($request->getSchemeAndHttpHost(), '/');

        if (str_starts_with($audioUrl, '/storage/')) {
            return $origin . $audioUrl;
        }

        if (str_starts_with($audioUrl, 'storage/')) {
            return $origin . '/' . $audioUrl;
        }

        if (preg_match('/^https?:\/\//i', $audioUrl)) {
            $path = parse_url($audioUrl, PHP_URL_PATH);
            if ($path && str_starts_with($path, '/storage/')) {
                return $origin . $path;
            }
        }

        return $audioUrl;
    }
}
