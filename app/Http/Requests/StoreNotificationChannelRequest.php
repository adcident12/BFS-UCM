<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationChannelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:email,webhook'],
            'events' => ['required', 'array', 'min:1'],
            'events.*' => ['string'],
            'is_active' => ['boolean'],
            'config.url' => ['required_if:type,webhook', 'nullable', 'url', 'max:500'],
            'config.secret' => ['nullable', 'string', 'max:200'],
            'config.to' => ['required_if:type,email', 'nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'config.url.required_if' => 'กรุณาระบุ Webhook URL',
            'config.url.url' => 'Webhook URL ไม่ถูกต้อง',
            'config.to.required_if' => 'กรุณาระบุอีเมลผู้รับ',
            'events.required' => 'กรุณาเลือกอย่างน้อย 1 event',
        ];
    }
}
