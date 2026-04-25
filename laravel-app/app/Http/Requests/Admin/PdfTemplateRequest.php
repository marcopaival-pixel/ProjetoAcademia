<?php

namespace App\Http\Requests\Admin;

use App\Enums\PdfDocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PdfTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'document_type' => ['required', 'string', Rule::in(PdfDocumentType::values())],
            'description' => ['nullable', 'string', 'max:2000'],
            'html_body' => ['required', 'string', 'max:500000'],
            'css_extra' => ['nullable', 'string', 'max:200000'],
            'primary_color' => ['nullable', 'regex:/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/'],
            'secondary_color' => ['nullable', 'regex:/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/'],
            'accent_color' => ['nullable', 'regex:/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/'],
            'is_active' => ['sometimes', 'boolean'],
            'is_default' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'logo' => ['nullable', 'image', 'max:4096', 'mimes:jpg,jpeg,png,gif,webp,svg'],
            'remove_logo' => ['sometimes', 'boolean'],
            'academy_company_id' => ['nullable', 'integer', 'exists:academy_companies,id'],
            'academy_unit_id' => ['nullable', 'integer', 'exists:academy_units,id'],
            'footer_html' => ['nullable', 'string', 'max:100000'],
            'auto_email_enabled' => ['sometimes', 'boolean'],
            'auto_email_recipients' => ['nullable', 'string', 'max:2000'],
            'auto_whatsapp_enabled' => ['sometimes', 'boolean'],
            'auto_whatsapp_recipients' => ['nullable', 'string', 'max:2000'],
            'whatsapp_message_template' => ['nullable', 'string', 'max:500'],
        ];
    }
}
