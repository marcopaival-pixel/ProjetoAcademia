<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\ClinicProtocol;
use App\Models\Especialidade;
use Illuminate\Http\Request;

class ProtocolController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user->academy_company_id) {
            return redirect()->route('dashboard')->with('error', 'Você não está vinculado a uma clínica.');
        }

        $protocols = ClinicProtocol::where('academy_company_id', $user->academy_company_id)
            ->with('specialty')
            ->get();

        return view('clinic.protocols.index', compact('protocols'));
    }

    public function create()
    {
        $specialties = Especialidade::active()->get();
        return view('clinic.protocols.create', compact('specialties'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'especialidade_id' => 'required|exists:especialidades,id',
            'type' => 'required|in:training,nutrition,medical',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'objective' => 'nullable|string',
            'protocol' => 'nullable|string',
            'frequency' => 'nullable|string',
            'duration' => 'nullable|string',
        ]);

        ClinicProtocol::create(array_merge($validated, [
            'academy_company_id' => $user->academy_company_id,
        ]));

        return redirect()->route('admin.clinic.protocols.index')->with('success', 'Protocolo cadastrado com sucesso.');
    }

    public function edit(ClinicProtocol $protocol)
    {
        $this->authorizeAccess($protocol);
        $specialties = Especialidade::active()->get();
        return view('clinic.protocols.edit', compact('protocol', 'specialties'));
    }

    public function update(Request $request, ClinicProtocol $protocol)
    {
        $this->authorizeAccess($protocol);

        $validated = $request->validate([
            'especialidade_id' => 'required|exists:especialidades,id',
            'type' => 'required|in:training,nutrition,medical',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'objective' => 'nullable|string',
            'protocol' => 'nullable|string',
            'frequency' => 'nullable|string',
            'duration' => 'nullable|string',
        ]);

        $protocol->update($validated);

        return redirect()->route('admin.clinic.protocols.index')->with('success', 'Protocolo atualizado com sucesso.');
    }

    public function destroy(ClinicProtocol $protocol)
    {
        $this->authorizeAccess($protocol);
        $protocol->delete();

        return redirect()->route('admin.clinic.protocols.index')->with('success', 'Protocolo removido.');
    }

    private function authorizeAccess(ClinicProtocol $protocol)
    {
        if ($protocol->academy_company_id !== auth()->user()->academy_company_id) {
            abort(403);
        }
    }
}
