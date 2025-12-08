<?php

namespace App\Livewire\Panel;

use Livewire\Component;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;

class AdminOrgSwitcher extends Component
{
    public $selectedOrgId = null;

    public function mount()
    {
        $this->selectedOrgId = session('admin_impersonated_org_id');
    }

    public function updatedSelectedOrgId($value)
    {
        if ($value) {
            session(['admin_impersonated_org_id' => $value]);
        } else {
            session()->forget('admin_impersonated_org_id');
        }

        // Reload to apply the global scope changes
        return $this->redirect(request()->header('Referer'), navigate: true);
    }

    public function clearSelection()
    {
        $this->selectedOrgId = null;
        session()->forget('admin_impersonated_org_id');
        return $this->redirect(request()->header('Referer'), navigate: true);
    }

    public function render()
    {
        // Only render if Super Admin
        if (!Auth::check() || !Auth::user()->is_super_admin) {
            return '<div></div>';
        }

        $organizations = Organization::orderBy('name')->get();

        return view('livewire.panel.admin-org-switcher', [
            'organizations' => $organizations
        ]);
    }
}
