<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Services\Operations\WorkerManagementService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Operations\WorkerManagementService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-a2bb25f6c93d01c114d5e9cf8edd7c40a84e727073de2a3848a3ed1bba494b73',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Operations\\WorkerManagementService',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Services/Operations/WorkerManagementService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Operations',
    'name' => 'App\\Services\\Operations\\WorkerManagementService',
    'shortName' => 'WorkerManagementService',
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
    'endLine' => 192,
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
      'lockKey' => 
      array (
        'declaringClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'implementingClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'name' => 'lockKey',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'worker_restart_lock\'',
          'attributes' => 
          array (
            'startLine' => 13,
            'endLine' => 13,
            'startTokenPos' => 44,
            'startFilePos' => 290,
            'endTokenPos' => 44,
            'endFilePos' => 310,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 13,
        'endLine' => 13,
        'startColumn' => 5,
        'endColumn' => 47,
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
      'restartAll' => 
      array (
        'name' => 'restartAll',
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
 * Restart all workers.
 */',
        'startLine' => 18,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Operations',
        'declaringClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'implementingClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'currentClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'aliasName' => NULL,
      ),
      'clearQueue' => 
      array (
        'name' => 'clearQueue',
        'parameters' => 
        array (
          'queue' => 
          array (
            'name' => 'queue',
            'default' => 
            array (
              'code' => '\'default\'',
              'attributes' => 
              array (
                'startLine' => 39,
                'endLine' => 39,
                'startTokenPos' => 210,
                'startFilePos' => 1158,
                'endTokenPos' => 210,
                'endFilePos' => 1166,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
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
            'startColumn' => 32,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
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
 * Clear a specific queue.
 */',
        'startLine' => 39,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Operations',
        'declaringClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'implementingClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'currentClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'aliasName' => NULL,
      ),
      'retryFailedJobs' => 
      array (
        'name' => 'retryFailedJobs',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => 
            array (
              'code' => '\'all\'',
              'attributes' => 
              array (
                'startLine' => 53,
                'endLine' => 53,
                'startTokenPos' => 340,
                'startFilePos' => 1671,
                'endTokenPos' => 340,
                'endFilePos' => 1675,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 53,
            'endLine' => 53,
            'startColumn' => 37,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
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
 * Retry failed jobs.
 */',
        'startLine' => 53,
        'endLine' => 62,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Operations',
        'declaringClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'implementingClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'currentClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'aliasName' => NULL,
      ),
      'flushFailedJobs' => 
      array (
        'name' => 'flushFailedJobs',
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
 * Flush all failed jobs.
 */',
        'startLine' => 67,
        'endLine' => 76,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Operations',
        'declaringClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'implementingClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'currentClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'aliasName' => NULL,
      ),
      'getActiveWorkers' => 
      array (
        'name' => 'getActiveWorkers',
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
 * Get active workers (Windows specific logic).
 */',
        'startLine' => 81,
        'endLine' => 88,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Operations',
        'declaringClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'implementingClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'currentClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'aliasName' => NULL,
      ),
      'getWindowsWorkers' => 
      array (
        'name' => 'getWindowsWorkers',
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
        'docComment' => NULL,
        'startLine' => 90,
        'endLine' => 125,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Services\\Operations',
        'declaringClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'implementingClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'currentClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'aliasName' => NULL,
      ),
      'getLinuxWorkers' => 
      array (
        'name' => 'getLinuxWorkers',
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
        'docComment' => NULL,
        'startLine' => 127,
        'endLine' => 156,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Services\\Operations',
        'declaringClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'implementingClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'currentClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'aliasName' => NULL,
      ),
      'getProcessMemoryWindows' => 
      array (
        'name' => 'getProcessMemoryWindows',
        'parameters' => 
        array (
          'pid' => 
          array (
            'name' => 'pid',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 158,
            'endLine' => 158,
            'startColumn' => 48,
            'endColumn' => 51,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 158,
        'endLine' => 172,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Services\\Operations',
        'declaringClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'implementingClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'currentClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'aliasName' => NULL,
      ),
      'logAudit' => 
      array (
        'name' => 'logAudit',
        'parameters' => 
        array (
          'action' => 
          array (
            'name' => 'action',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 174,
            'endLine' => 174,
            'startColumn' => 33,
            'endColumn' => 46,
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
        'docComment' => NULL,
        'startLine' => 174,
        'endLine' => 181,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Services\\Operations',
        'declaringClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'implementingClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'currentClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'aliasName' => NULL,
      ),
      'acquireLock' => 
      array (
        'name' => 'acquireLock',
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
        'docComment' => NULL,
        'startLine' => 183,
        'endLine' => 186,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Services\\Operations',
        'declaringClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'implementingClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'currentClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'aliasName' => NULL,
      ),
      'releaseLock' => 
      array (
        'name' => 'releaseLock',
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
        'startLine' => 188,
        'endLine' => 191,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Services\\Operations',
        'declaringClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'implementingClassName' => 'App\\Services\\Operations\\WorkerManagementService',
        'currentClassName' => 'App\\Services\\Operations\\WorkerManagementService',
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