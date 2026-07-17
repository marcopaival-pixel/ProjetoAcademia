<?php

return [
    'provider' => env('AI_EVOLUTION_PROVIDER', 'local'),

    'openai' => [
        'comparison_model' => env('OPENAI_COMPARISON_MODEL', env('OPENAI_MODEL_MAIN', 'gpt-4o')),
        'audit_model' => env('OPENAI_AUDIT_MODEL', env('OPENAI_MODEL_MAIN', 'gpt-4o')),
        'store' => filter_var(env('OPENAI_EVOLUTION_STORE', false), FILTER_VALIDATE_BOOL),
    ],

    'thresholds' => [
        'photo_quality_min' => (int) env('AI_EVOLUTION_PHOTO_QUALITY_MIN', 75),
        'claim_confidence_min' => (float) env('AI_EVOLUTION_CLAIM_CONFIDENCE_MIN', 0.70),
        'claim_consistent_min' => (float) env('AI_EVOLUTION_CLAIM_CONSISTENT_MIN', 0.85),
    ],

    'allowed_claim_types' => [
        'objective_metric',
        'visual_observation',
        'limitation',
        'data_quality_warning',
    ],

    'allowed_body_regions' => [
        'abdomen',
        'waist',
        'arms',
        'legs',
        'shoulders',
        'back',
        'general_silhouette',
    ],

    'forbidden_terms' => [
        'diagnostico',
        'diagnóstico',
        'doenca',
        'doença',
        'obesidade',
        'sobrepeso',
        'retencao',
        'retenção',
        'inflamacao',
        'inflamação',
        'percentual de gordura',
        'gordura corporal estimada',
        'ganho muscular confirmado',
        'massa muscular confirmada',
        'postura errada',
        'postura inadequada',
        'assimetria clinica',
        'assimetria clínica',
    ],
];
