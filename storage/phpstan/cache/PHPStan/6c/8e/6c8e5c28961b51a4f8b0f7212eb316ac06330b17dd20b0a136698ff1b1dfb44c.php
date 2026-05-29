<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Models\AIVisionLog.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\AIVisionLog
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-d28d0a8e95ae234fe82a0d7f4aeec949a095dcf5dc2f433253f20ed3a93f0701',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\AIVisionLog',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Models/AIVisionLog.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\AIVisionLog',
    'shortName' => 'AIVisionLog',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 10,
    'endLine' => 51,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'App\\Models\\Traits\\FillsTenantColumns',
      1 => 'App\\Models\\Traits\\HasClinic',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\AIVisionLog',
        'implementingClassName' => 'App\\Models\\AIVisionLog',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'ai_vision_logs\'',
          'attributes' => 
          array (
            'startLine' => 14,
            'endLine' => 14,
            'startTokenPos' => 46,
            'startFilePos' => 244,
            'endTokenPos' => 46,
            'endFilePos' => 259,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 14,
        'endLine' => 14,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\AIVisionLog',
        'implementingClassName' => 'App\\Models\\AIVisionLog',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'orchestrator_log_id\', \'user_id\', \'clinic_id\', \'academy_company_id\', \'document_type\', \'confidence\', \'image_path\', \'image_hash\', \'extracted_data\', \'warnings\', \'model_name\', \'total_tokens\', \'cost_usd\', \'execution_time_ms\']',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 31,
            'startTokenPos' => 55,
            'startFilePos' => 289,
            'endTokenPos' => 98,
            'endFilePos' => 627,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 31,
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
        'declaringClassName' => 'App\\Models\\AIVisionLog',
        'implementingClassName' => 'App\\Models\\AIVisionLog',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'extracted_data\' => \'array\', \'warnings\' => \'array\', \'confidence\' => \'float\', \'cost_usd\' => \'decimal:6\', \'total_tokens\' => \'integer\', \'execution_time_ms\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 33,
            'endLine' => 40,
            'startTokenPos' => 107,
            'startFilePos' => 654,
            'endTokenPos' => 151,
            'endFilePos' => 875,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 40,
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
      'orchestratorLog' => 
      array (
        'name' => 'orchestratorLog',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
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
        'declaringClassName' => 'App\\Models\\AIVisionLog',
        'implementingClassName' => 'App\\Models\\AIVisionLog',
        'currentClassName' => 'App\\Models\\AIVisionLog',
        'aliasName' => NULL,
      ),
      'user' => 
      array (
        'name' => 'user',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
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
        'declaringClassName' => 'App\\Models\\AIVisionLog',
        'implementingClassName' => 'App\\Models\\AIVisionLog',
        'currentClassName' => 'App\\Models\\AIVisionLog',
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