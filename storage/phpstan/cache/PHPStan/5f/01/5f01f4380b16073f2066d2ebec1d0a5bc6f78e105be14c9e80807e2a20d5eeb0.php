<?php declare(strict_types = 1);

// osfsl-C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../spatie/laravel-backup/src/Commands/BackupCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Spatie\Backup\Commands\BackupCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-b1c9957077b7436f615dd441c73b76f04faca232480cee1077e29f74396d3401-8.2.12-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Spatie\\Backup\\Commands\\BackupCommand',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../spatie/laravel-backup/src/Commands/BackupCommand.php',
      ),
    ),
    'namespace' => 'Spatie\\Backup\\Commands',
    'name' => 'Spatie\\Backup\\Commands\\BackupCommand',
    'shortName' => 'BackupCommand',
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
    'endLine' => 116,
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
        'declaringClassName' => 'Spatie\\Backup\\Commands\\BackupCommand',
        'implementingClassName' => 'Spatie\\Backup\\Commands\\BackupCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'backup:run {--filename=} {--only-db} {--db-name=*} {--only-files} {--only-to-disk=} {--disable-notifications} {--timeout=} {--tries=}\'',
          'attributes' => 
          array (
            'startLine' => 18,
            'endLine' => 18,
            'startTokenPos' => 72,
            'startFilePos' => 462,
            'endTokenPos' => 72,
            'endFilePos' => 596,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 18,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 163,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'Spatie\\Backup\\Commands\\BackupCommand',
        'implementingClassName' => 'Spatie\\Backup\\Commands\\BackupCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Run the backup.\'',
          'attributes' => 
          array (
            'startLine' => 20,
            'endLine' => 20,
            'startTokenPos' => 81,
            'startFilePos' => 629,
            'endTokenPos' => 81,
            'endFilePos' => 645,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 20,
        'endLine' => 20,
        'startColumn' => 5,
        'endColumn' => 47,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'config' => 
      array (
        'declaringClassName' => 'Spatie\\Backup\\Commands\\BackupCommand',
        'implementingClassName' => 'Spatie\\Backup\\Commands\\BackupCommand',
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
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 33,
        'endColumn' => 56,
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
            'startLine' => 22,
            'endLine' => 22,
            'startColumn' => 33,
            'endColumn' => 56,
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
        'startLine' => 22,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\Backup\\Commands',
        'declaringClassName' => 'Spatie\\Backup\\Commands\\BackupCommand',
        'implementingClassName' => 'Spatie\\Backup\\Commands\\BackupCommand',
        'currentClassName' => 'Spatie\\Backup\\Commands\\BackupCommand',
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
        'startLine' => 27,
        'endLine' => 102,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\Backup\\Commands',
        'declaringClassName' => 'Spatie\\Backup\\Commands\\BackupCommand',
        'implementingClassName' => 'Spatie\\Backup\\Commands\\BackupCommand',
        'currentClassName' => 'Spatie\\Backup\\Commands\\BackupCommand',
        'aliasName' => NULL,
      ),
      'guardAgainstInvalidOptions' => 
      array (
        'name' => 'guardAgainstInvalidOptions',
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
        'docComment' => NULL,
        'startLine' => 104,
        'endLine' => 115,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Spatie\\Backup\\Commands',
        'declaringClassName' => 'Spatie\\Backup\\Commands\\BackupCommand',
        'implementingClassName' => 'Spatie\\Backup\\Commands\\BackupCommand',
        'currentClassName' => 'Spatie\\Backup\\Commands\\BackupCommand',
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