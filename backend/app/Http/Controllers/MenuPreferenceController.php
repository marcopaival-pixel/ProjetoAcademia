<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\UserMenuPreference;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuPreferenceController extends Controller
{
    protected $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    /**
     * Show the menu personalization page.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Garante que o cache seja limpo ao entrar na página de configuração
        $this->menuService->clearCache($user->id);
        
        // Busca apenas os menus que pertencem ao perfil do usuário (ex: Aluno só vê menus de Aluno)
        // Passamos true em ignorePreferences para listar todos os disponíveis para o perfil, mesmo os ocultos
        $menus = $this->menuService->getMenusForUser($user, true);

        return view('menu-preferences.index', compact('menus'));
    }

    /**
     * Save the user's menu preferences.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $visibleMenus = $request->input('menus', []);

        DB::transaction(function () use ($user, $visibleMenus) {
            $allMenus = Menu::all();

            foreach ($allMenus as $menu) {
                // Os menus obrigatórios não podem ser ocultados
                if ($menu->is_required) {
                    continue;
                }

                UserMenuPreference::updateOrCreate(
                    ['user_id' => $user->id, 'menu_id' => $menu->id],
                    ['visible' => isset($visibleMenus[$menu->id])]
                );
            }
        });

        // Limpa o cache para que a sidebar reflita as mudanças no próximo carregamento
        $this->menuService->clearCache($user->id);

        return redirect()->route('menu.preferences.index')->with('success', 'Preferências de menu atualizadas com sucesso!');
    }

    /**
     * Restore the system's default menu configuration.
     */
    public function restore()
    {
        $user = auth()->user();
        
        UserMenuPreference::where('user_id', $user->id)->delete();
        
        $this->menuService->clearCache($user->id);

        return redirect()->back()->with('success', 'Menus padrão restaurados com sucesso.');
    }
}
