<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Http\Controllers\PlanoController.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Http\Controllers\PlanoController
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-cdd61d78db40ea37fdbf5d2bf08b386d9987a959d405ea1d02d03f5254b83811',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Http\\Controllers\\PlanoController',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Http/Controllers/PlanoController.php',
      ),
    ),
    'namespace' => 'App\\Http\\Controllers',
    'name' => 'App\\Http\\Controllers\\PlanoController',
    'shortName' => 'PlanoController',
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
    'endLine' => 93,
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
      'TAB_META' => 
      array (
        'declaringClassName' => 'App\\Http\\Controllers\\PlanoController',
        'implementingClassName' => 'App\\Http\\Controllers\\PlanoController',
        'name' => 'TAB_META',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'aluno\' => [\'icon\' => \'user-round\', \'label\' => \'Aluno\'], \'student\' => [\'icon\' => \'user-round\', \'label\' => \'Aluno\'], \'personal\' => [\'icon\' => \'stethoscope\', \'label\' => \'Profissional\'], \'nutricionista\' => [\'icon\' => \'salad\', \'label\' => \'Nutricionista\'], \'nutritionist\' => [\'icon\' => \'salad\', \'label\' => \'Nutricionista\'], \'professional\' => [\'icon\' => \'stethoscope\', \'label\' => \'Profissional\'], \'academia\' => [\'icon\' => \'building-2\', \'label\' => \'Clínica\'], \'clinic\' => [\'icon\' => \'building-2\', \'label\' => \'Clínica\'], \'full\' => [\'icon\' => \'layers\', \'label\' => \'Completo\']]',
          'attributes' => 
          array (
            'startLine' => 15,
            'endLine' => 25,
            'startTokenPos' => 47,
            'startFilePos' => 329,
            'endTokenPos' => 228,
            'endFilePos' => 1031,
          ),
        ),
        'docComment' => '/**
 * Labels e ícones legíveis por tipo/role — usados na view para montar as tabs.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 15,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
      'GROUP_MAPPING' => 
      array (
        'declaringClassName' => 'App\\Http\\Controllers\\PlanoController',
        'implementingClassName' => 'App\\Http\\Controllers\\PlanoController',
        'name' => 'GROUP_MAPPING',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'student\' => \'aluno\', \'clinic\' => \'academia\', \'manager\' => \'academia\', \'nutritionist\' => \'nutricionista\']',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 35,
            'startTokenPos' => 241,
            'startFilePos' => 1178,
            'endTokenPos' => 271,
            'endFilePos' => 1338,
          ),
        ),
        'docComment' => '/**
 * Mapeamento de normalização para evitar confusão entre slugs técnicos e exibição.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 30,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      '__invoke' => 
      array (
        'name' => '__invoke',
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
            'startLine' => 37,
            'endLine' => 37,
            'startColumn' => 30,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'mp' => 
          array (
            'name' => 'mp',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\MercadoPagoService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 37,
            'endLine' => 37,
            'startColumn' => 48,
            'endColumn' => 69,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\View\\View',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 37,
        'endLine' => 92,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers',
        'declaringClassName' => 'App\\Http\\Controllers\\PlanoController',
        'implementingClassName' => 'App\\Http\\Controllers\\PlanoController',
        'currentClassName' => 'App\\Http\\Controllers\\PlanoController',
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