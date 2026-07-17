<?php

return [
    'auto_apply_min_confidence' => (float) env('WORKOUT_SCAN_AUTO_APPLY_MIN_CONFIDENCE', 0.85),

    'regression_sample_size' => (int) env('WORKOUT_SCAN_REGRESSION_SAMPLE_SIZE', 10),

    'auto_applicable_types' => [
        'image_structure',
        'image_readability',
        'ocr_preprocessing',
    ],

    'human_review_types' => [
        'exercise_content',
        'load_content',
        'sets_content',
        'repetitions_content',
        'medical_content',
        'nutrition_content',
    ],
];
