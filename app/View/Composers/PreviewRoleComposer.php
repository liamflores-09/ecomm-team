<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class PreviewRoleComposer
{
    public function compose(View $view): void
    {
        $isPreview = false;
        $previewRole = null;

        if (Auth::check() && Auth::user()->isAdmin() && session()->has('preview_role')) {
            $previewRole = session('preview_role');
            $isPreview = true;
        }

        $view->with('isPreview', $isPreview)
             ->with('previewRole', $previewRole);
    }
}
