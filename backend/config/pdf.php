<?php

return [

    'historico_disk' => env('PDF_HISTORICO_DISK', 'local'),

    'historico_directory' => env('PDF_HISTORICO_DIRECTORY', 'historico-pdfs'),

    /** Segmento de URL público para validação (sem barra inicial). */
    'validation_path_segment' => env('PDF_VALIDATION_PATH', 'validar-documento'),

    /** Dias até expiração da validação (0 = sem expiração automática). */
    'default_ttl_days' => (int) env('PDF_DEFAULT_TTL_DAYS', 0),

];
