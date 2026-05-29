<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Http\Controllers\Support\TrainingController.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Http\Controllers\Support\TrainingController
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-5f05e4ebb6489e7a656ebbe7a03eb9708ffcc54441c4ed5e92920351cdb287b4',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Http\\Controllers\\Support\\TrainingController',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Http/Controllers/Support/TrainingController.php',
      ),
    ),
    'namespace' => 'App\\Http\\Controllers\\Support',
    'name' => 'App\\Http\\Controllers\\Support\\TrainingController',
    'shortName' => 'TrainingController',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 11,
    'endLine' => 111,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'App\\Http\\Controllers\\Controller',
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
    ),
    'immediateMethods' => 
    array (
      'index' => 
      array (
        'name' => 'index',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Display a listing of the training modules.
 */',
        'startLine' => 16,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Support',
        'declaringClassName' => 'App\\Http\\Controllers\\Support\\TrainingController',
        'implementingClassName' => 'App\\Http\\Controllers\\Support\\TrainingController',
        'currentClassName' => 'App\\Http\\Controllers\\Support\\TrainingController',
        'aliasName' => NULL,
      ),
      'showModule' => 
      array (
        'name' => 'showModule',
        'parameters' => 
        array (
          'module' => 
          array (
            'name' => 'module',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\TrainingModule',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 43,
            'endLine' => 43,
            'startColumn' => 32,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Display the specified module and its lessons.
 */',
        'startLine' => 43,
        'endLine' => 62,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Support',
        'declaringClassName' => 'App\\Http\\Controllers\\Support\\TrainingController',
        'implementingClassName' => 'App\\Http\\Controllers\\Support\\TrainingController',
        'currentClassName' => 'App\\Http\\Controllers\\Support\\TrainingController',
        'aliasName' => NULL,
      ),
      'showLesson' => 
      array (
        'name' => 'showLesson',
        'parameters' => 
        array (
          'module' => 
          array (
            'name' => 'module',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\TrainingModule',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 67,
            'endLine' => 67,
            'startColumn' => 32,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'lesson' => 
          array (
            'name' => 'lesson',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\TrainingLesson',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 67,
            'endLine' => 67,
            'startColumn' => 56,
            'endColumn' => 77,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Display a specific lesson and play its video.
 */',
        'startLine' => 67,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Support',
        'declaringClassName' => 'App\\Http\\Controllers\\Support\\TrainingController',
        'implementingClassName' => 'App\\Http\\Controllers\\Support\\TrainingController',
        'currentClassName' => 'App\\Http\\Controllers\\Support\\TrainingController',
        'aliasName' => NULL,
      ),
      'toggleCompletion' => 
      array (
        'name' => 'toggleCompletion',
        'parameters' => 
        array (
          'lesson' => 
          array (
            'name' => 'lesson',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\TrainingLesson',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 91,
            'endLine' => 91,
            'startColumn' => 38,
            'endColumn' => 59,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Toggle lesson completion status.
 */',
        'startLine' => 91,
        'endLine' => 110,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Support',
        'declaringClassName' => 'App\\Http\\Controllers\\Support\\TrainingController',
        'implementingClassName' => 'App\\Http\\Controllers\\Support\\TrainingController',
        'currentClassName' => 'App\\Http\\Controllers\\Support\\TrainingController',
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