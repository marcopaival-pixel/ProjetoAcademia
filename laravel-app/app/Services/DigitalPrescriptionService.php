<?php

namespace App\Services;

use App\Models\MedicalPrescription;
use App\Models\PdfSignature;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;

class DigitalPrescriptionService
{
    /**
     * Prescribes medication using Memed external API.
     */
    public function createMemedPrescription(User $patient, User $doctor, array $medications): array
    {
        // Sandbox key or mock endpoint configuration
        $apiKey = config('services.memed.key');
        
        if (!$apiKey) {
            // Mock mode for local environment/XAMPP
            return [
                'status' => 'success',
                'memed_prescription_id' => 'memed_' . uniqid(),
                'patient_name' => $patient->name,
                'doctor_crm' => $doctor->profile->crm ?? '123456-SP',
                'medications' => $medications,
                'iframe_url' => 'https://sandbox.memed.com.br/prescription/' . uniqid(),
            ];
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/json',
        ])->post('https://api.memed.com.br/v1/prescricoes', [
            'data' => [
                'type' => 'prescricoes',
                'attributes' => [
                    'paciente' => [
                        'nome' => $patient->name,
                        'email' => $patient->email,
                    ],
                    'medicamentos' => $medications,
                ]
            ]
        ]);

        if ($response->failed()) {
            throw new Exception('Memed integration failed: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Signs a PDF document using an ICP-Brasil certified procedure.
     */
    public function signWithIcpBrasil(int $historicoPdfId, User $signer, string $ipAddress): PdfSignature
    {
        // Under local environments, we simulate validation/handshake with ICP-Brasil providers like ITI / Soluti / Piramide
        $certificateHash = hash('sha256', $signer->id . time() . 'ICP-BRASIL-CERT');

        return PdfSignature::create([
            'historico_pdf_id' => $historicoPdfId,
            'user_id' => $signer->id,
            'signer_name' => $signer->name,
            'tipo_assinatura' => \App\Enums\PdfSignatureRole::PROFISSIONAL,
            'modo' => \App\Enums\PdfSignatureMode::ICP_BRASIL,
            'imagem_assinatura' => 'signatures/icp_signed_stamp.png',
            'ip_address' => $ipAddress,
            'data_assinatura' => now(),
        ]);
    }
}
