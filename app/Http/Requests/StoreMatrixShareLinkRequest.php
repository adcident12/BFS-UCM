<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMatrixShareLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()?->canAccess('share_link_manage');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'label'               => ['required', 'string', 'max:100'],
            'expires_days'        => ['required', 'integer', 'min:1', 'max:365'],
            'filter_system_ids'   => ['nullable', 'array'],
            'filter_system_ids.*' => ['integer', 'exists:systems,id'],
            'filter_departments'  => ['nullable', 'array'],
            'filter_departments.*' => ['string', 'max:100'],
            'filter_usernames'    => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'label.required'             => 'กรุณาระบุชื่อ Share Link',
            'expires_days.required'      => 'กรุณาระบุอายุลิงก์',
            'expires_days.min'           => 'อายุลิงก์ต้องมากกว่า 0 วัน',
            'expires_days.max'           => 'อายุลิงก์ไม่เกิน 365 วัน',
            'filter_system_ids.*.exists' => 'ระบบที่เลือกไม่ถูกต้อง',
        ];
    }
}
