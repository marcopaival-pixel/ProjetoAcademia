<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View|\Illuminate\Http\RedirectResponse
    {
        // Removido o redirecionamento automático para permitir que administradores 
        // e utilizadores logados vejam a página inicial institucional se assim desejarem.
        
        return view('home');
    }
}
