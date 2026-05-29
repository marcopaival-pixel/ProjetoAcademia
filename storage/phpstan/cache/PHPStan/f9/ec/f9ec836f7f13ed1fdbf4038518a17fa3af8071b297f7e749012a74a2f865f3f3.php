<?php declare(strict_types = 1);

// osfsl-C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../laravel/pulse/src/Commands/CheckCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Laravel\Pulse\Commands\CheckCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-491482534a163f0af185d5cee5e20294e3d008ae43ef1b00e45f487e99e5ef57-8.2.12-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Laravel\\Pulse\\Commands\\CheckCommand',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../laravel/pulse/src/Commands/CheckCommand.php',
      ),
    ),
    'namespace' => 'Laravel\\Pulse\\Commands',
    'name' => 'Laravel\\Pulse\\Commands\\CheckCommand',
    'shortName' => 'CheckCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @internal
 */',
    'attributes' => 
    array (
      0 => 
      array (
        'name' => 'Symfony\\Component\\Console\\Attribute\\AsCommand',
        'isRepeated' => false,
        'arguments' => 
        array (
          'name' => 
          array (
            'code' => '\'pulse:check\'',
            'attributes' => 
            array (
              'startLine' => 23,
              'endLine' => 23,
              'startTokenPos' => 85,
              'startFilePos' => 599,
              'endTokenPos' => 85,
              'endFilePos' => 611,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 23,
    'endLine' => 92,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Console\\Command',
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
      'signature' => 
      array (
        'declaringClassName' => 'Laravel\\Pulse\\Commands\\CheckCommand',
        'implementingClassName' => 'Laravel\\Pulse\\Commands\\CheckCommand',
        'name' => 'signature',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'pulse:check {--once : Take a single snapshot}\'',
          'attributes' => 
          array (
            'startLine' => 31,
            'endLine' => 31,
            'startTokenPos' => 107,
            'startFilePos' => 750,
            'endTokenPos' => 107,
            'endFilePos' => 796,
          ),
        ),
        'docComment' => '/**
 * The command\'s signature.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 72,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'Laravel\\Pulse\\Commands\\CheckCommand',
        'implementingClassName' => 'Laravel\\Pulse\\Commands\\CheckCommand',
        'name' => 'description',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Take a snapshot of the current server\\\'s pulse\'',
          'attributes' => 
          array (
            'startLine' => 38,
            'endLine' => 38,
            'startTokenPos' => 118,
            'startFilePos' => 902,
            'endTokenPos' => 118,
            'endFilePos' => 949,
          ),
        ),
        'docComment' => '/**
 * The command\'s description.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 75,
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
      'handle' => 
      array (
        'name' => 'handle',
        'parameters' => 
        array (
          'pulse' => 
          array (
            'name' => 'pulse',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Laravel\\Pulse\\Pulse',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 44,
            'endLine' => 44,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'cache' => 
          array (
            'name' => 'cache',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Laravel\\Pulse\\Support\\CacheStoreResolver',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 45,
            'endLine' => 45,
            'startColumn' => 9,
            'endColumn' => 33,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'event' => 
          array (
            'name' => 'event',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Contracts\\Events\\Dispatcher',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 46,
            'endLine' => 46,
            'startColumn' => 9,
            'endColumn' => 25,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Handle the command.
 */',
        'startLine' => 43,
        'endLine' => 81,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Laravel\\Pulse\\Commands',
        'declaringClassName' => 'Laravel\\Pulse\\Commands\\CheckCommand',
        'implementingClassName' => 'Laravel\\Pulse\\Commands\\CheckCommand',
        'currentClassName' => 'Laravel\\Pulse\\Commands\\CheckCommand',
        'aliasName' => NULL,
      ),
      'ensureTelescopeEntriesAreCollected' => 
      array (
        'name' => 'ensureTelescopeEntriesAreCollected',
        'parameters' => 
        array (
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
 * Schedule Telescope to store entries if enabled.
 */',
        'startLine' => 86,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Laravel\\Pulse\\Commands',
        'declaringClassName' => 'Laravel\\Pulse\\Commands\\CheckCommand',
        'implementingClassName' => 'Laravel\\Pulse\\Commands\\CheckCommand',
        'currentClassName' => 'Laravel\\Pulse\\Commands\\CheckCommand',
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