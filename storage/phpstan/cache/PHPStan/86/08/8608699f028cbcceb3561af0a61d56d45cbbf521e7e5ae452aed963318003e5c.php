<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Models\PatientAccessToken.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\PatientAccessToken
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-c4b9fe91059f967796fd1ac35ba688675193811af010afd2e5596a1edbaaa890',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\PatientAccessToken',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Models/PatientAccessToken.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\PatientAccessToken',
    'shortName' => 'PatientAccessToken',
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
    'endLine' => 51,
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
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\PatientAccessToken',
        'implementingClassName' => 'App\\Models\\PatientAccessToken',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'patient_id\', \'type\', \'token_hash\', \'expires_at\', \'used_at\', \'status\']',
          'attributes' => 
          array (
            'startLine' => 10,
            'endLine' => 17,
            'startTokenPos' => 33,
            'startFilePos' => 192,
            'endTokenPos' => 53,
            'endFilePos' => 317,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 10,
        'endLine' => 17,
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
        'declaringClassName' => 'App\\Models\\PatientAccessToken',
        'implementingClassName' => 'App\\Models\\PatientAccessToken',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'expires_at\' => \'datetime\', \'used_at\' => \'datetime\']',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 22,
            'startTokenPos' => 62,
            'startFilePos' => 344,
            'endTokenPos' => 78,
            'endFilePos' => 419,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 19,
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
        'docComment' => '/**
 * Relacionamento com o paciente (User).
 */',
        'startLine' => 27,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\PatientAccessToken',
        'implementingClassName' => 'App\\Models\\PatientAccessToken',
        'currentClassName' => 'App\\Models\\PatientAccessToken',
        'aliasName' => NULL,
      ),
      'isValid' => 
      array (
        'name' => 'isValid',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Verifica se o token é válido.
 */',
        'startLine' => 35,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\PatientAccessToken',
        'implementingClassName' => 'App\\Models\\PatientAccessToken',
        'currentClassName' => 'App\\Models\\PatientAccessToken',
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