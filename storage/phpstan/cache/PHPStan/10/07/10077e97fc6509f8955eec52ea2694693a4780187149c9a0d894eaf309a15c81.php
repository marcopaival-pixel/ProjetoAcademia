<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Services\Operations\OperationalControlService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Operations\OperationalControlService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-2c76306378365d8b448788d3aca1b809337c73cd17b829f06fc99a42363e03c3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Operations\\OperationalControlService',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Services/Operations/OperationalControlService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Operations',
    'name' => 'App\\Services\\Operations\\OperationalControlService',
    'shortName' => 'OperationalControlService',
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
    'endLine' => 80,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
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
      'cacheKey' => 
      array (
        'declaringClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'implementingClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'name' => 'cacheKey',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'operational_settings\'',
          'attributes' => 
          array (
            'startLine' => 10,
            'endLine' => 10,
            'startTokenPos' => 29,
            'startFilePos' => 178,
            'endTokenPos' => 29,
            'endFilePos' => 199,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 10,
        'endLine' => 10,
        'startColumn' => 5,
        'endColumn' => 49,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'filePath' => 
      array (
        'declaringClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'implementingClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'name' => 'filePath',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 11,
        'endLine' => 11,
        'startColumn' => 5,
        'endColumn' => 24,
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
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 13,
        'endLine' => 16,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Operations',
        'declaringClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'implementingClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'currentClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'aliasName' => NULL,
      ),
      'getSettings' => 
      array (
        'name' => 'getSettings',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get current operational settings.
 */',
        'startLine' => 21,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Operations',
        'declaringClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'implementingClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'currentClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'aliasName' => NULL,
      ),
      'updateSettings' => 
      array (
        'name' => 'updateSettings',
        'parameters' => 
        array (
          'newSettings' => 
          array (
            'name' => 'newSettings',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 40,
            'endLine' => 40,
            'startColumn' => 36,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Update operational settings.
 */',
        'startLine' => 40,
        'endLine' => 52,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Operations',
        'declaringClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'implementingClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'currentClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'aliasName' => NULL,
      ),
      'isMaintenance' => 
      array (
        'name' => 'isMaintenance',
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
 * Is the system in maintenance mode?
 */',
        'startLine' => 57,
        'endLine' => 60,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Operations',
        'declaringClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'implementingClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'currentClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'aliasName' => NULL,
      ),
      'isReadOnly' => 
      array (
        'name' => 'isReadOnly',
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
 * Is the system in read-only mode?
 */',
        'startLine' => 65,
        'endLine' => 68,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Operations',
        'declaringClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'implementingClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'currentClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'aliasName' => NULL,
      ),
      'canBypass' => 
      array (
        'name' => 'canBypass',
        'parameters' => 
        array (
          'user' => 
          array (
            'name' => 'user',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 73,
            'endLine' => 73,
            'startColumn' => 31,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
 * Check if a user can bypass maintenance.
 */',
        'startLine' => 73,
        'endLine' => 79,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Operations',
        'declaringClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'implementingClassName' => 'App\\Services\\Operations\\OperationalControlService',
        'currentClassName' => 'App\\Services\\Operations\\OperationalControlService',
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