<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Models\ProfessionalProfile.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\ProfessionalProfile
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-e19e98ae11acd84f716cbd18da9291ef56efaa0a7ddcd87866d49c8828464620',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\ProfessionalProfile',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Models/ProfessionalProfile.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\ProfessionalProfile',
    'shortName' => 'ProfessionalProfile',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 9,
    'endLine' => 97,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'App\\Models\\Traits\\HasClinic',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\ProfessionalProfile',
        'implementingClassName' => 'App\\Models\\ProfessionalProfile',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'user_id\', \'profession_id\', \'specialty\', \'about\', \'service_types\', \'appointment_duration\', \'appointment_interval\', \'company_name\', \'registration_number\', \'council\', \'registration_uf\', \'registration_expiry_date\', \'document_path\', \'signature_path\', \'created_by\', \'updated_by\', \'document_version\', \'experience_years\', \'education\', \'professional_photo_path\', \'offered_services\', \'consultation_price\', \'clinic_address\', \'clinic_city\', \'clinic_state\', \'work_days\', \'work_start_time\', \'work_end_time\', \'is_public\']',
          'attributes' => 
          array (
            'startLine' => 13,
            'endLine' => 43,
            'startTokenPos' => 43,
            'startFilePos' => 239,
            'endTokenPos' => 132,
            'endFilePos' => 985,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 13,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'casts' => 
      array (
        'declaringClassName' => 'App\\Models\\ProfessionalProfile',
        'implementingClassName' => 'App\\Models\\ProfessionalProfile',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'registration_expiry_date\' => \'date\', \'service_types\' => \'array\', \'work_days\' => \'array\', \'is_public\' => \'boolean\', \'consultation_price\' => \'decimal:2\']',
          'attributes' => 
          array (
            'startLine' => 45,
            'endLine' => 51,
            'startTokenPos' => 141,
            'startFilePos' => 1012,
            'endTokenPos' => 178,
            'endFilePos' => 1211,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 45,
        'endLine' => 51,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      'user' => 
      array (
        'name' => 'user',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 53,
        'endLine' => 56,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ProfessionalProfile',
        'implementingClassName' => 'App\\Models\\ProfessionalProfile',
        'currentClassName' => 'App\\Models\\ProfessionalProfile',
        'aliasName' => NULL,
      ),
      'profession' => 
      array (
        'name' => 'profession',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 58,
        'endLine' => 61,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ProfessionalProfile',
        'implementingClassName' => 'App\\Models\\ProfessionalProfile',
        'currentClassName' => 'App\\Models\\ProfessionalProfile',
        'aliasName' => NULL,
      ),
      'creator' => 
      array (
        'name' => 'creator',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 63,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ProfessionalProfile',
        'implementingClassName' => 'App\\Models\\ProfessionalProfile',
        'currentClassName' => 'App\\Models\\ProfessionalProfile',
        'aliasName' => NULL,
      ),
      'updater' => 
      array (
        'name' => 'updater',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 68,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ProfessionalProfile',
        'implementingClassName' => 'App\\Models\\ProfessionalProfile',
        'currentClassName' => 'App\\Models\\ProfessionalProfile',
        'aliasName' => NULL,
      ),
      'daysUntilExpiry' => 
      array (
        'name' => 'daysUntilExpiry',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'int',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Verifica se o registro profissional está próximo do vencimento.
 * Retorna os dias restantes ou null.
 */',
        'startLine' => 77,
        'endLine' => 84,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ProfessionalProfile',
        'implementingClassName' => 'App\\Models\\ProfessionalProfile',
        'currentClassName' => 'App\\Models\\ProfessionalProfile',
        'aliasName' => NULL,
      ),
      'getExpiryWarningAttribute' => 
      array (
        'name' => 'getExpiryWarningAttribute',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'string',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 86,
        'endLine' => 96,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ProfessionalProfile',
        'implementingClassName' => 'App\\Models\\ProfessionalProfile',
        'currentClassName' => 'App\\Models\\ProfessionalProfile',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));