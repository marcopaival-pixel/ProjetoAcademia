<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PhotoGalleryController extends Controller
{
    /**
     * Retorna a galeria de fotos.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $studentId = $request->input('student_id', $user->id);

        // Segurança: O aluno só acessa as próprias fotos
        // O profissional só acessa de seus alunos (aqui simplificado, ideal via vínculo)
        if ($user->profile_id == 2 && $studentId != $user->id) { // Assumindo profile_id 2 = Aluno
            return response()->json(['error' => 'Você só pode acessar suas próprias fotos.'], 403);
        }

        // Determinar o plano usando a estrutura existente
        $isPremium = $user->hasPremiumAccess();

        $query = Photo::where('student_id', $studentId);

        if (!$isPremium) {
            // Histórico limitado aos últimos 30 dias no plano Free
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        }

        $photos = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'photos' => $photos,
            'is_premium' => $isPremium,
            'message' => !$isPremium ? 'Visualizando histórico dos últimos 30 dias (Plano Free).' : 'Visualizando histórico completo (Premium).'
        ]);
    }

    /**
     * Upload de uma nova foto.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'photo' => 'required|image|max:5120', // máximo 5MB
            'category' => 'nullable|string',
            'description' => 'nullable|string'
        ]);

        $studentId = $user->profile_id == 2 ? $user->id : $request->input('student_id', $user->id);

        // Verificação do plano
        $isPremium = $user->hasPremiumAccess();

        if (!$isPremium) {
            // Conta as fotos atuais do aluno
            $photoCount = Photo::where('student_id', $studentId)->count();
            if ($photoCount >= 10) {
                return response()->json([
                    'error' => 'Você atingiu o limite de fotos do plano Free. Faça upgrade para o plano Premium para liberar armazenamento ilimitado e mais funcionalidades.',
                    'action' => 'upgrade_premium'
                ], 403);
            }

            // Não permite o uso de categorias avançadas no plano Free
            if ($request->has('category') && $request->input('category') !== 'Geral') {
                return response()->json([
                    'error' => 'A organização por categorias estruturadas é exclusiva do plano Premium.',
                    'action' => 'upgrade_premium'
                ], 403);
            }
        }

        $path = $request->file('photo')->store('gallery_photos', 'public');

        $photo = Photo::create([
            'student_id' => $studentId,
            'file_path' => $path,
            'category' => $isPremium ? $request->input('category', 'Geral') : 'Geral',
            'description' => $request->input('description'),
            'plan_type' => $isPremium ? 'Premium' : 'Free'
        ]);

        return response()->json([
            'success' => 'Foto carregada com sucesso.',
            'photo' => $photo
        ]);
    }

    /**
     * Excluir uma foto.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $photo = Photo::findOrFail($id);

        // Segurança
        if ($user->profile_id == 2 && $photo->student_id != $user->id) {
            return response()->json(['error' => 'Ação não permitida.'], 403);
        }

        Storage::disk('public')->delete($photo->file_path);
        logger()->info("Photo deleted by User {$user->id}", ['photo_id' => $photo->id]);
        $photo->delete();

        return response()->json(['success' => 'Foto removida com sucesso.']);
    }

    /**
     * Comparação lado a lado (Apenas Premium).
     */
    public function compare(Request $request)
    {
        $user = Auth::user();
        $isPremium = $user->hasPremiumAccess();

        if (!$isPremium) {
            return response()->json([
                'error' => 'A comparação de fotos lado a lado é uma funcionalidade exclusiva do plano Premium. Faça upgrade para acessá-la.',
                'action' => 'upgrade_premium'
            ], 403);
        }

        $request->validate([
            'photo_id_1' => 'required|exists:photos,id',
            'photo_id_2' => 'required|exists:photos,id',
        ]);

        $photo1 = Photo::findOrFail($request->photo_id_1);
        $photo2 = Photo::findOrFail($request->photo_id_2);

        // Segurança (simplificada)
        if ($user->profile_id == 2 && ($photo1->student_id != $user->id || $photo2->student_id != $user->id)) {
            return response()->json(['error' => 'Você não tem permissão para comparar essas fotos.'], 403);
        }

        return response()->json([
            'photo_before' => $photo1,
            'photo_after' => $photo2,
            'success' => 'Comparação gerada.'
        ]);
    }
}
