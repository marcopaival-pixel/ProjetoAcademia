<?php declare(strict_types = 1);

// osfsl-C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../spatie/laravel-backup/src/Commands/CleanupCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Spatie\Backup\Commands\CleanupCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-3bca052cf6fc12e192371a2280b268bc3d43c56f7b96c5951ef3cdd4e3068136-8.2.12-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Spatie\\Backup\\Commands\\CleanupCommand',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../spatie/laravel-backup/src/Commands/CleanupCommand.php',
      ),
    ),
    'namespace' => 'Spatie\\Backup\\Commands',
    'name' => 'Spatie\\Backup\\Commands\\CleanupCommand',
    'shortName' => 'CleanupCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 14,
    'endLine' => 67,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Spatie\\Backup\\Commands\\BaseCommand',
    'implementsClassNames' => 
    array (
      0 => 'Illuminate\\Contracts\\Console\\Isolatable',
    ),
    'traitClassNames' => 
    array (
      0 => 'Spatie\\Backup\\Traits\\Retryable',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'signature' => 
      array (
        'declaringClassName' => 'Spatie\\Backup\\Commands\\CleanupCommand',
        'implementingClassName' => 'Spatie\\Backup\\Commands\\CleanupCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'backup:clean {--disable-notifications} {--tries=}\'',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 19,
            'startTokenPos' => 74,
            'startFilePos' => 505,
            'endTokenPos' => 74,
            'endFilePos' => 555,
          ),
        ),
        'docComment' => '/** @var string */',
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 5,
        'endColumn' => 79,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'Spatie\\Backup\\Commands\\CleanupCommand',
        'implementingClassName' => 'Spatie\\Backup\\Commands\\CleanupCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Remove all backups older than specified number of days in config.\'',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 85,
            'startFilePos' => 611,
            'endTokenPos' => 85,
            'endFilePos' => 677,
          ),
        ),
        'docComment' => '/** @var string */',
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 97,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'strategy' => 
      array (
        'declaringClassName' => 'Spatie\\Backup\\Commands\\CleanupCommand',
        'implementingClassName' => 'Spatie\\Backup\\Commands\\CleanupCommand',
        'name' => 'strategy',
        'modifiers' => 2,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Spatie\\Backup\\Tasks\\Cleanup\\CleanupStrategy',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 25,
        'endLine' => 25,
        'startColumn' => 9,
        'endColumn' => 43,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'config' => 
      array (
        'declaringClassName' => 'Spatie\\Backup\\Commands\\CleanupCommand',
        'implementingClassName' => 'Spatie\\Backup\\Commands\\CleanupCommand',
        'name' => 'config',
        'modifiers' => 2,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Spatie\\Backup\\Config\\Config',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 9,
        'endColumn' => 32,
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
          'strategy' => 
          array (
            'name' => 'strategy',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Spatie\\Backup\\Tasks\\Cleanup\\CleanupStrategy',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 25,
            'endLine' => 25,
            'startColumn' => 9,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'config' => 
          array (
            'name' => 'config',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Spatie\\Backup\\Config\\Config',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 26,
            'endLine' => 26,
            'startColumn' => 9,
            'endColumn' => 32,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 24,
        'endLine' => 29,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\Backup\\Commands',
        'declaringClassName' => 'Spatie\\Backup\\Commands\\CleanupCommand',
        'implementingClassName' => 'Spatie\\Backup\\Commands\\CleanupCommand',
        'currentClassName' => 'Spatie\\Backup\\Commands\\CleanupCommand',
        'aliasName' => NULL,
      ),
      'handle' => 
      array (
        'name' => 'handle',
        'parameters' => 
        array (
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
        'docComment' => NULL,
        'startLine' => 31,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\Backup\\Commands',
        'declaringClassName' => 'Spatie\\Backup\\Commands\\CleanupCommand',
        'implementingClassName' => 'Spatie\\Backup\\Commands\\CleanupCommand',
        'currentClassName' => 'Spatie\\Backup\\Commands\\CleanupCommand',
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