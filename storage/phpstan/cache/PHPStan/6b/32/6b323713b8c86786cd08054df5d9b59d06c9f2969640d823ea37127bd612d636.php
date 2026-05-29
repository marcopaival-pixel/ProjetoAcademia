<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Models\WithdrawalRequest.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\WithdrawalRequest
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-264f395b2a42d1f449bc3f42eb91e6f0123987b96e43d24738021810bea6d033',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\WithdrawalRequest',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Models/WithdrawalRequest.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\WithdrawalRequest',
    'shortName' => 'WithdrawalRequest',
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
    'endLine' => 37,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
    ),
    'immediateConstants' => 
    array (
      'STATUS_PENDENTE' => 
      array (
        'declaringClassName' => 'App\\Models\\WithdrawalRequest',
        'implementingClassName' => 'App\\Models\\WithdrawalRequest',
        'name' => 'STATUS_PENDENTE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'PENDENTE\'',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 28,
            'startTokenPos' => 100,
            'startFilePos' => 551,
            'endTokenPos' => 100,
            'endFilePos' => 560,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATUS_APROVADO' => 
      array (
        'declaringClassName' => 'App\\Models\\WithdrawalRequest',
        'implementingClassName' => 'App\\Models\\WithdrawalRequest',
        'name' => 'STATUS_APROVADO',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'APROVADO\'',
          'attributes' => 
          array (
            'startLine' => 29,
            'endLine' => 29,
            'startTokenPos' => 109,
            'startFilePos' => 591,
            'endTokenPos' => 109,
            'endFilePos' => 600,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 29,
        'endLine' => 29,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATUS_PAGO' => 
      array (
        'declaringClassName' => 'App\\Models\\WithdrawalRequest',
        'implementingClassName' => 'App\\Models\\WithdrawalRequest',
        'name' => 'STATUS_PAGO',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'PAGO\'',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 118,
            'startFilePos' => 627,
            'endTokenPos' => 118,
            'endFilePos' => 632,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 31,
      ),
      'STATUS_RECUSADO' => 
      array (
        'declaringClassName' => 'App\\Models\\WithdrawalRequest',
        'implementingClassName' => 'App\\Models\\WithdrawalRequest',
        'name' => 'STATUS_RECUSADO',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'RECUSADO\'',
          'attributes' => 
          array (
            'startLine' => 31,
            'endLine' => 31,
            'startTokenPos' => 127,
            'startFilePos' => 663,
            'endTokenPos' => 127,
            'endFilePos' => 672,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\WithdrawalRequest',
        'implementingClassName' => 'App\\Models\\WithdrawalRequest',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'representative_id\', \'amount\', \'pix_key\', \'bank_info\', \'status\', \'admin_notes\', \'paid_at\']',
          'attributes' => 
          array (
            'startLine' => 13,
            'endLine' => 21,
            'startTokenPos' => 43,
            'startFilePos' => 267,
            'endTokenPos' => 66,
            'endFilePos' => 420,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 13,
        'endLine' => 21,
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
        'declaringClassName' => 'App\\Models\\WithdrawalRequest',
        'implementingClassName' => 'App\\Models\\WithdrawalRequest',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'amount\' => \'decimal:2\', \'paid_at\' => \'datetime\']',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 26,
            'startTokenPos' => 75,
            'startFilePos' => 447,
            'endTokenPos' => 91,
            'endFilePos' => 519,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 26,
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
      'representative' => 
      array (
        'name' => 'representative',
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
        'startLine' => 33,
        'endLine' => 36,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\WithdrawalRequest',
        'implementingClassName' => 'App\\Models\\WithdrawalRequest',
        'currentClassName' => 'App\\Models\\WithdrawalRequest',
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