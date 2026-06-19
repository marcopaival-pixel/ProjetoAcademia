<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use App\Models\User;
use App\Services\SecureFileService;
use App\Support\PatientAccessGuard;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PhotoGalleryController extends Controller
{
    public function __construct(
        private SecureFileService $secureFiles
    ) {}

    /**
     * Retorna a galeria de fotos.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $studentId = (int) $request->input('student_id', $user->id);

        PatientAccessGuard::assertStudentDataAccess($user, $studentId);

        $isPremium = $user->hasPremiumAccess();

        $query = Photo::where('student_id', $studentId);

        if (!$isPremium) {
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
            'photo' => 'required|image|max:5120',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
            'student_id' => 'nullable|integer|exists:users,id',
        ]);

        $studentId = $user->profile_id == 2 ? $user->id : (int) $request->input('student_id', $user->id);
        PatientAccessGuard::assertStudentDataAccess($user, $studentId);

        $isPremium = $user->hasPremiumAccess();

        if (!$isPremium) {
            $photoCount = Photo::where('student_id', $studentId)->count();
            if ($photoCount >= 10) {
                return response()->json([
                    'error' => 'Você atingiu o limite de fotos do plano Free. Faça upgrade para o plano Premium para liberar armazenamento ilimitado e mais funcionalidades.',
                    'action' => 'upgrade_premium'
                ], 403);
            }

            if ($request->has('category') && $request->input('category') !== 'Geral') {
                return response()->json([
                    'error' => 'A organização por categorias estruturadas é exclusiva do plano Premium.',
                    'action' => 'upgrade_premium'
                ], 403);
            }
        }

        $path = $this->secureFiles->storeSensitiveFile($request->file('photo'), 'gallery_photos');

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

        PatientAccessGuard::assertStudentDataAccess($user, (int) $photo->student_id);

        $this->secureFiles->deleteFile($photo->file_path);
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

        PatientAccessGuard::assertStudentDataAccess($user, (int) $photo1->student_id);
        PatientAccessGuard::assertStudentDataAccess($user, (int) $photo2->student_id);

        if ((int) $photo1->student_id !== (int) $photo2->student_id) {
            return response()->json(['error' => 'As fotos devem pertencer ao mesmo aluno.'], 403);
        }

        return response()->json([
            'photo_before' => $photo1,
            'photo_after' => $photo2,
            'success' => 'Comparação gerada.'
        ]);
    }
}
