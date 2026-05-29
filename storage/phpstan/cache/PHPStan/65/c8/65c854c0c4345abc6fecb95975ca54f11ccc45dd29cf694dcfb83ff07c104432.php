<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Http\Controllers\Professional\AIPrescriptionController.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Http\Controllers\Professional\AIPrescriptionController
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-7020c5f35a762f89c494ee5f3f9070377ddc5e81bf3f2df5034e03fc5dbb46aa',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Http\\Controllers\\Professional\\AIPrescriptionController',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Http/Controllers/Professional/AIPrescriptionController.php',
      ),
    ),
    'namespace' => 'App\\Http\\Controllers\\Professional',
    'name' => 'App\\Http\\Controllers\\Professional\\AIPrescriptionController',
    'shortName' => 'AIPrescriptionController',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 18,
    'endLine' => 215,
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
      'orchestrator' => 
      array (
        'declaringClassName' => 'App\\Http\\Controllers\\Professional\\AIPrescriptionController',
        'implementingClassName' => 'App\\Http\\Controllers\\Professional\\AIPrescriptionController',
        'name' => 'orchestrator',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\AI\\OrchestratorService',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 9,
        'endColumn' => 49,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
          'orchestrator' => 
          array (
            'name' => 'orchestrator',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\AI\\OrchestratorService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 21,
            'endLine' => 21,
            'startColumn' => 9,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 20,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Professional',
        'declaringClassName' => 'App\\Http\\Controllers\\Professional\\AIPrescriptionController',
        'implementingClassName' => 'App\\Http\\Controllers\\Professional\\AIPrescriptionController',
        'currentClassName' => 'App\\Http\\Controllers\\Professional\\AIPrescriptionController',
        'aliasName' => NULL,
      ),
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
 * Exibe o Assistente de Prescrição Dinâmica.
 */',
        'startLine' => 26,
        'endLine' => 60,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Professional',
        'declaringClassName' => 'App\\Http\\Controllers\\Professional\\AIPrescriptionController',
        'implementingClassName' => 'App\\Http\\Controllers\\Professional\\AIPrescriptionController',
        'currentClassName' => 'App\\Http\\Controllers\\Professional\\AIPrescriptionController',
        'aliasName' => NULL,
      ),
      'generate' => 
      array (
        'name' => 'generate',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\Request',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 66,
            'endLine' => 66,
            'startColumn' => 30,
            'endColumn' => 45,
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
 * Gera uma sugestão de plano via IA.
 */',
        'startLine' => 66,
        'endLine' => 143,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Professional',
        'declaringClassName' => 'App\\Http\\Controllers\\Professional\\AIPrescriptionController',
        'implementingClassName' => 'App\\Http\\Controllers\\Professional\\AIPrescriptionController',
        'currentClassName' => 'App\\Http\\Controllers\\Professional\\AIPrescriptionController',
        'aliasName' => NULL,
      ),
      'store' => 
      array (
        'name' => 'store',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\Request',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 148,
            'endLine' => 148,
            'startColumn' => 27,
            'endColumn' => 42,
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
 * Salva a prescrição no prontuário.
 */',
        'startLine' => 148,
        'endLine' => 177,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Professional',
        'declaringClassName' => 'App\\Http\\Controllers\\Professional\\AIPrescriptionController',
        'implementingClassName' => 'App\\Http\\Controllers\\Professional\\AIPrescriptionController',
        'currentClassName' => 'App\\Http\\Controllers\\Professional\\AIPrescriptionController',
        'aliasName' => NULL,
      ),
      'getMockResponse' => 
      array (
        'name' => 'getMockResponse',
        'parameters' => 
        array (
          'type' => 
          array (
            'name' => 'type',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 179,
            'endLine' => 179,
            'startColumn' => 38,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'userPrompt' => 
          array (
            'name' => 'userPrompt',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 179,
            'endLine' => 179,
            'startColumn' => 45,
            'endColumn' => 55,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'specialty' => 
          array (
            'name' => 'specialty',
            'default' => 
            array (
              'code' => '\'Geral\'',
              'attributes' => 
              array (
                'startLine' => 179,
                'endLine' => 179,
                'startTokenPos' => 1376,
                'startFilePos' => 6843,
                'endTokenPos' => 1376,
                'endFilePos' => 6849,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 179,
            'endLine' => 179,
            'startColumn' => 58,
            'endColumn' => 77,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 179,
        'endLine' => 214,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Http\\Controllers\\Professional',
        'declaringClassName' => 'App\\Http\\Controllers\\Professional\\AIPrescriptionController',
        'implementingClassName' => 'App\\Http\\Controllers\\Professional\\AIPrescriptionController',
        'currentClassName' => 'App\\Http\\Controllers\\Professional\\AIPrescriptionController',
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