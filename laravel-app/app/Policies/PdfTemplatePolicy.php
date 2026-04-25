<?php

namespace App\Policies;

use App\Models\PdfTemplate;
use App\Models\User;

class PdfTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdministrator() || $user->hasPermission('pdf.templates.manage');
    }

    public function view(User $user, PdfTemplate $pdfTemplate): bool
    {
        return $this->viewAny($user) && $this->sameTenant($user, $pdfTemplate);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, PdfTemplate $pdfTemplate): bool
    {
        return $this->viewAny($user) && $this->sameTenant($user, $pdfTemplate);
    }

    public function delete(User $user, PdfTemplate $pdfTemplate): bool
    {
        return $this->viewAny($user) && $this->sameTenant($user, $pdfTemplate);
    }

    private function sameTenant(User $user, PdfTemplate $template): bool
    {
        if ($user->isAdministrator()) {
            return true;
        }
        $tid = $template->academy_company_id;
        if ($tid === null) {
            return true;
        }

        return (int) $user->academy_company_id === (int) $tid;
    }
}
