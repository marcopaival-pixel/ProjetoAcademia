<?php declare(strict_types = 1);

// osfsl-C:/Projetos/ProjetoAcademia/laravel-app/app/Models/TrainingPlan.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\TrainingPlan
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-42fcf8a91ea2955740bc1278af7e803dd0005e6efa3d3f02d7e01e2c3bd275b4-8.2.12-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\TrainingPlan',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Models/TrainingPlan.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\TrainingPlan',
    'shortName' => 'TrainingPlan',
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
    'endLine' => 59,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      1 => 'App\\Models\\Traits\\FiltersByProfessional',
      2 => 'App\\Models\\Traits\\HasClinic',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\TrainingPlan',
        'implementingClassName' => 'App\\Models\\TrainingPlan',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'user_id\', \'professional_id\', \'creator_id\', \'name\', \'plan_label\', \'description\', \'goal\', \'frequency\', \'difficulty\', \'estimated_duration\', \'is_active\', \'student_profile\', \'split_type\', \'status\', \'days_of_week\', \'is_template\', \'total_volume\', \'muscles_worked\', \'created_by_ai\']',
          'attributes' => 
          array (
            'startLine' => 15,
            'endLine' => 35,
            'startTokenPos' => 54,
            'startFilePos' => 363,
            'endTokenPos' => 113,
            'endFilePos' => 797,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 15,
        'endLine' => 35,
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
        'declaringClassName' => 'App\\Models\\TrainingPlan',
        'implementingClassName' => 'App\\Models\\TrainingPlan',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'days_of_week\' => \'array\', \'muscles_worked\' => \'array\', \'is_active\' => \'boolean\', \'is_template\' => \'boolean\', \'created_by_ai\' => \'boolean\']',
          'attributes' => 
          array (
            'startLine' => 37,
            'endLine' => 43,
            'startTokenPos' => 122,
            'startFilePos' => 824,
            'endTokenPos' => 159,
            'endFilePos' => 1010,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 37,
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
        'startLine' => 45,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\TrainingPlan',
        'implementingClassName' => 'App\\Models\\TrainingPlan',
        'currentClassName' => 'App\\Models\\TrainingPlan',
        'aliasName' => NULL,
      ),
      'exercises' => 
      array (
        'name' => 'exercises',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 50,
        'endLine' => 53,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\TrainingPlan',
        'implementingClassName' => 'App\\Models\\TrainingPlan',
        'currentClassName' => 'App\\Models\\TrainingPlan',
        'aliasName' => NULL,
      ),
      'targetAreas' => 
      array (
        'name' => 'targetAreas',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 55,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\TrainingPlan',
        'implementingClassName' => 'App\\Models\\TrainingPlan',
        'currentClassName' => 'App\\Models\\TrainingPlan',
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