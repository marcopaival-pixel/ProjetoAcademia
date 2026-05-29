<?php declare(strict_types = 1);

// osfsl-C:/Projetos/ProjetoAcademia/laravel-app/app/Models/Commission.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Commission
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6054dbc9c8622596aa2545aa816d4d82684ada9fe147e430676bdabd1d2cb5c2-8.2.12-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Commission',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Models/Commission.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\Commission',
    'shortName' => 'Commission',
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
    'endLine' => 56,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'STATUS_PENDENTE' => 
      array (
        'declaringClassName' => 'App\\Models\\Commission',
        'implementingClassName' => 'App\\Models\\Commission',
        'name' => 'STATUS_PENDENTE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'PENDENTE\'',
          'attributes' => 
          array (
            'startLine' => 32,
            'endLine' => 32,
            'startTokenPos' => 123,
            'startFilePos' => 704,
            'endTokenPos' => 123,
            'endFilePos' => 713,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATUS_DISPONIVEL' => 
      array (
        'declaringClassName' => 'App\\Models\\Commission',
        'implementingClassName' => 'App\\Models\\Commission',
        'name' => 'STATUS_DISPONIVEL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'DISPONIVEL\'',
          'attributes' => 
          array (
            'startLine' => 33,
            'endLine' => 33,
            'startTokenPos' => 132,
            'startFilePos' => 746,
            'endTokenPos' => 132,
            'endFilePos' => 757,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 43,
      ),
      'STATUS_PAGO' => 
      array (
        'declaringClassName' => 'App\\Models\\Commission',
        'implementingClassName' => 'App\\Models\\Commission',
        'name' => 'STATUS_PAGO',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'PAGO\'',
          'attributes' => 
          array (
            'startLine' => 34,
            'endLine' => 34,
            'startTokenPos' => 141,
            'startFilePos' => 784,
            'endTokenPos' => 141,
            'endFilePos' => 789,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 34,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 31,
      ),
      'STATUS_CANCELADO' => 
      array (
        'declaringClassName' => 'App\\Models\\Commission',
        'implementingClassName' => 'App\\Models\\Commission',
        'name' => 'STATUS_CANCELADO',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'CANCELADO\'',
          'attributes' => 
          array (
            'startLine' => 35,
            'endLine' => 35,
            'startTokenPos' => 150,
            'startFilePos' => 821,
            'endTokenPos' => 150,
            'endFilePos' => 831,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 35,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Commission',
        'implementingClassName' => 'App\\Models\\Commission',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'representative_id\', \'user_id\', \'payment_id\', \'subscription_id\', \'base_amount\', \'commission_rate\', \'commission_amount\', \'status\', \'available_at\', \'paid_at\', \'notes\']',
          'attributes' => 
          array (
            'startLine' => 10,
            'endLine' => 22,
            'startTokenPos' => 33,
            'startFilePos' => 184,
            'endTokenPos' => 68,
            'endFilePos' => 444,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 10,
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
        'declaringClassName' => 'App\\Models\\Commission',
        'implementingClassName' => 'App\\Models\\Commission',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'base_amount\' => \'decimal:2\', \'commission_rate\' => \'decimal:2\', \'commission_amount\' => \'decimal:2\', \'available_at\' => \'datetime\', \'paid_at\' => \'datetime\']',
          'attributes' => 
          array (
            'startLine' => 24,
            'endLine' => 30,
            'startTokenPos' => 77,
            'startFilePos' => 471,
            'endTokenPos' => 114,
            'endFilePos' => 672,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 30,
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
        'startLine' => 37,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Commission',
        'implementingClassName' => 'App\\Models\\Commission',
        'currentClassName' => 'App\\Models\\Commission',
        'aliasName' => NULL,
      ),
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
        'startLine' => 42,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Commission',
        'implementingClassName' => 'App\\Models\\Commission',
        'currentClassName' => 'App\\Models\\Commission',
        'aliasName' => NULL,
      ),
      'payment' => 
      array (
        'name' => 'payment',
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
        'startLine' => 47,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Commission',
        'implementingClassName' => 'App\\Models\\Commission',
        'currentClassName' => 'App\\Models\\Commission',
        'aliasName' => NULL,
      ),
      'subscription' => 
      array (
        'name' => 'subscription',
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
        'startLine' => 52,
        'endLine' => 55,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Commission',
        'implementingClassName' => 'App\\Models\\Commission',
        'currentClassName' => 'App\\Models\\Commission',
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