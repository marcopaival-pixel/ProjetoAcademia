<?php declare(strict_types = 1);

// osfsl-C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../laravel/pulse/src/Commands/WorkCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Laravel\Pulse\Commands\WorkCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-2b3697256e545cf328d6df6cbc4f7ec70654bbd7636568060613443b857ccd2b-8.2.12-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Laravel\\Pulse\\Commands\\WorkCommand',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../laravel/pulse/src/Commands/WorkCommand.php',
      ),
    ),
    'namespace' => 'Laravel\\Pulse\\Commands',
    'name' => 'Laravel\\Pulse\\Commands\\WorkCommand',
    'shortName' => 'WorkCommand',
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
            'code' => '\'pulse:work\'',
            'attributes' => 
            array (
              'startLine' => 17,
              'endLine' => 17,
              'startTokenPos' => 55,
              'startFilePos' => 378,
              'endTokenPos' => 55,
              'endFilePos' => 389,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 17,
    'endLine' => 79,
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
        'declaringClassName' => 'Laravel\\Pulse\\Commands\\WorkCommand',
        'implementingClassName' => 'Laravel\\Pulse\\Commands\\WorkCommand',
        'name' => 'signature',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'pulse:work {--stop-when-empty : Stop when the stream is empty}\'',
          'attributes' => 
          array (
            'startLine' => 25,
            'endLine' => 25,
            'startTokenPos' => 77,
            'startFilePos' => 527,
            'endTokenPos' => 77,
            'endFilePos' => 590,
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
        'startLine' => 25,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 89,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'Laravel\\Pulse\\Commands\\WorkCommand',
        'implementingClassName' => 'Laravel\\Pulse\\Commands\\WorkCommand',
        'name' => 'description',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Process incoming Pulse data from the ingest stream\'',
          'attributes' => 
          array (
            'startLine' => 32,
            'endLine' => 32,
            'startTokenPos' => 88,
            'startFilePos' => 696,
            'endTokenPos' => 88,
            'endFilePos' => 747,
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
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 79,
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
            'startLine' => 38,
            'endLine' => 38,
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
            'startLine' => 39,
            'endLine' => 39,
            'startColumn' => 9,
            'endColumn' => 33,
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
        'startLine' => 37,
        'endLine' => 68,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Laravel\\Pulse\\Commands',
        'declaringClassName' => 'Laravel\\Pulse\\Commands\\WorkCommand',
        'implementingClassName' => 'Laravel\\Pulse\\Commands\\WorkCommand',
        'currentClassName' => 'Laravel\\Pulse\\Commands\\WorkCommand',
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
        'startLine' => 73,
        'endLine' => 78,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Laravel\\Pulse\\Commands',
        'declaringClassName' => 'Laravel\\Pulse\\Commands\\WorkCommand',
        'implementingClassName' => 'Laravel\\Pulse\\Commands\\WorkCommand',
        'currentClassName' => 'Laravel\\Pulse\\Commands\\WorkCommand',
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