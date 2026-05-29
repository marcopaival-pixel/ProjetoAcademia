<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Models\MedicalCertificate.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\MedicalCertificate
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-51726aad28c6eb09578c2e603cadbc6929d4ac76fc474f508de83483b8c51ca7',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\MedicalCertificate',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Models/MedicalCertificate.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\MedicalCertificate',
    'shortName' => 'MedicalCertificate',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 8,
    'endLine' => 39,
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
        'declaringClassName' => 'App\\Models\\MedicalCertificate',
        'implementingClassName' => 'App\\Models\\MedicalCertificate',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'patient_id\', \'professional_id\', \'date\', \'reason\', \'start_date\', \'end_date\', \'period\', \'observations\', \'pdf_path\']',
          'attributes' => 
          array (
            'startLine' => 12,
            'endLine' => 22,
            'startTokenPos' => 38,
            'startFilePos' => 219,
            'endTokenPos' => 67,
            'endFilePos' => 412,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 12,
        'endLine' => 22,
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
        'declaringClassName' => 'App\\Models\\MedicalCertificate',
        'implementingClassName' => 'App\\Models\\MedicalCertificate',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'date\' => \'datetime\', \'start_date\' => \'date\', \'end_date\' => \'date\']',
          'attributes' => 
          array (
            'startLine' => 24,
            'endLine' => 28,
            'startTokenPos' => 76,
            'startFilePos' => 439,
            'endTokenPos' => 99,
            'endFilePos' => 537,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 28,
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
      'patient' => 
      array (
        'name' => 'patient',
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
        'startLine' => 30,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\MedicalCertificate',
        'implementingClassName' => 'App\\Models\\MedicalCertificate',
        'currentClassName' => 'App\\Models\\MedicalCertificate',
        'aliasName' => NULL,
      ),
      'professional' => 
      array (
        'name' => 'professional',
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
        'startLine' => 35,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\MedicalCertificate',
        'implementingClassName' => 'App\\Models\\MedicalCertificate',
        'currentClassName' => 'App\\Models\\MedicalCertificate',
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