<?php declare(strict_types = 1);

// osfsl-C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../laravel/pulse/src/Commands/ClearCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Laravel\Pulse\Commands\ClearCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-9f9fff0b01d6ca16bcda27af05dd56aaddd8ce067630a843215e6344ad83702b-8.2.12-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Laravel\\Pulse\\Commands\\ClearCommand',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../laravel/pulse/src/Commands/ClearCommand.php',
      ),
    ),
    'namespace' => 'Laravel\\Pulse\\Commands',
    'name' => 'Laravel\\Pulse\\Commands\\ClearCommand',
    'shortName' => 'ClearCommand',
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
            'code' => '\'pulse:clear\'',
            'attributes' => 
            array (
              'startLine' => 13,
              'endLine' => 13,
              'startTokenPos' => 35,
              'startFilePos' => 231,
              'endTokenPos' => 35,
              'endFilePos' => 243,
            ),
          ),
          'aliases' => 
          array (
            'code' => '[\'pulse:purge\']',
            'attributes' => 
            array (
              'startLine' => 13,
              'endLine' => 13,
              'startTokenPos' => 41,
              'startFilePos' => 255,
              'endTokenPos' => 43,
              'endFilePos' => 269,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 13,
    'endLine' => 63,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Console\\Command',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Console\\ConfirmableTrait',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'signature' => 
      array (
        'declaringClassName' => 'Laravel\\Pulse\\Commands\\ClearCommand',
        'implementingClassName' => 'Laravel\\Pulse\\Commands\\ClearCommand',
        'name' => 'signature',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'pulse:clear {--type=* : Only clear the specified type(s)}
                                     {--force : Force the operation to run when in production}\'',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 24,
            'startTokenPos' => 70,
            'startFilePos' => 435,
            'endTokenPos' => 70,
            'endFilePos' => 588,
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
        'startLine' => 23,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 96,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'Laravel\\Pulse\\Commands\\ClearCommand',
        'implementingClassName' => 'Laravel\\Pulse\\Commands\\ClearCommand',
        'name' => 'description',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Delete all Pulse data from storage\'',
          'attributes' => 
          array (
            'startLine' => 31,
            'endLine' => 31,
            'startTokenPos' => 81,
            'startFilePos' => 694,
            'endTokenPos' => 81,
            'endFilePos' => 729,
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
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 63,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'aliases' => 
      array (
        'declaringClassName' => 'Laravel\\Pulse\\Commands\\ClearCommand',
        'implementingClassName' => 'Laravel\\Pulse\\Commands\\ClearCommand',
        'name' => 'aliases',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'pulse:purge\']',
          'attributes' => 
          array (
            'startLine' => 38,
            'endLine' => 38,
            'startTokenPos' => 92,
            'startFilePos' => 853,
            'endTokenPos' => 94,
            'endFilePos' => 867,
          ),
        ),
        'docComment' => '/**
 * The console command name aliases.
 *
 * @var array<int, string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 41,
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
            'startLine' => 43,
            'endLine' => 43,
            'startColumn' => 28,
            'endColumn' => 39,
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
        'endLine' => 62,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Laravel\\Pulse\\Commands',
        'declaringClassName' => 'Laravel\\Pulse\\Commands\\ClearCommand',
        'implementingClassName' => 'Laravel\\Pulse\\Commands\\ClearCommand',
        'currentClassName' => 'Laravel\\Pulse\\Commands\\ClearCommand',
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