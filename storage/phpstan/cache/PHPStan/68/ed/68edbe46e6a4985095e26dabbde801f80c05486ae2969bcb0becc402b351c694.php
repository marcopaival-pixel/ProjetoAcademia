<?php declare(strict_types = 1);

// osfsl-C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../laravel/framework/src/Illuminate/Process/InvokedProcess.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Process\InvokedProcess
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-5ba1fb987b74128f7aec3be54656079b2c037ab98eabf68feabbf5dc4f18d686-8.2.12-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Process\\InvokedProcess',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../laravel/framework/src/Illuminate/Process/InvokedProcess.php',
      ),
    ),
    'namespace' => 'Illuminate\\Process',
    'name' => 'Illuminate\\Process\\InvokedProcess',
    'shortName' => 'InvokedProcess',
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
    'endLine' => 152,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'Illuminate\\Contracts\\Process\\InvokedProcess',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'process' => 
      array (
        'declaringClassName' => 'Illuminate\\Process\\InvokedProcess',
        'implementingClassName' => 'Illuminate\\Process\\InvokedProcess',
        'name' => 'process',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The underlying process instance.
 *
 * @var \\Symfony\\Component\\Process\\Process
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 17,
        'endLine' => 17,
        'startColumn' => 5,
        'endColumn' => 23,
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
          'process' => 
          array (
            'name' => 'process',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Symfony\\Component\\Process\\Process',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 25,
            'endLine' => 25,
            'startColumn' => 33,
            'endColumn' => 48,
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
 * Create a new invoked process instance.
 *
 * @param  \\Symfony\\Component\\Process\\Process  $process
 * @return void
 */',
        'startLine' => 25,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\InvokedProcess',
        'implementingClassName' => 'Illuminate\\Process\\InvokedProcess',
        'currentClassName' => 'Illuminate\\Process\\InvokedProcess',
        'aliasName' => NULL,
      ),
      'id' => 
      array (
        'name' => 'id',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the process ID if the process is still running.
 *
 * @return int|null
 */',
        'startLine' => 35,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\InvokedProcess',
        'implementingClassName' => 'Illuminate\\Process\\InvokedProcess',
        'currentClassName' => 'Illuminate\\Process\\InvokedProcess',
        'aliasName' => NULL,
      ),
      'signal' => 
      array (
        'name' => 'signal',
        'parameters' => 
        array (
          'signal' => 
          array (
            'name' => 'signal',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
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
            'startColumn' => 28,
            'endColumn' => 38,
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
 * Send a signal to the process.
 *
 * @param  int  $signal
 * @return $this
 */',
        'startLine' => 46,
        'endLine' => 51,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\InvokedProcess',
        'implementingClassName' => 'Illuminate\\Process\\InvokedProcess',
        'currentClassName' => 'Illuminate\\Process\\InvokedProcess',
        'aliasName' => NULL,
      ),
      'stop' => 
      array (
        'name' => 'stop',
        'parameters' => 
        array (
          'timeout' => 
          array (
            'name' => 'timeout',
            'default' => 
            array (
              'code' => '10',
              'attributes' => 
              array (
                'startLine' => 60,
                'endLine' => 60,
                'startTokenPos' => 149,
                'startFilePos' => 1357,
                'endTokenPos' => 149,
                'endFilePos' => 1358,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 60,
            'endLine' => 60,
            'startColumn' => 26,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'signal' => 
          array (
            'name' => 'signal',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 60,
                'endLine' => 60,
                'startTokenPos' => 159,
                'startFilePos' => 1376,
                'endTokenPos' => 159,
                'endFilePos' => 1379,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'int',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 60,
            'endLine' => 60,
            'startColumn' => 47,
            'endColumn' => 65,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Stop the process if it is still running.
 *
 * @param  float  $timeout
 * @param  int|null  $signal
 * @return int|null
 */',
        'startLine' => 60,
        'endLine' => 63,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\InvokedProcess',
        'implementingClassName' => 'Illuminate\\Process\\InvokedProcess',
        'currentClassName' => 'Illuminate\\Process\\InvokedProcess',
        'aliasName' => NULL,
      ),
      'running' => 
      array (
        'name' => 'running',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the process is still running.
 *
 * @return bool
 */',
        'startLine' => 70,
        'endLine' => 73,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\InvokedProcess',
        'implementingClassName' => 'Illuminate\\Process\\InvokedProcess',
        'currentClassName' => 'Illuminate\\Process\\InvokedProcess',
        'aliasName' => NULL,
      ),
      'output' => 
      array (
        'name' => 'output',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the standard output for the process.
 *
 * @return string
 */',
        'startLine' => 80,
        'endLine' => 83,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\InvokedProcess',
        'implementingClassName' => 'Illuminate\\Process\\InvokedProcess',
        'currentClassName' => 'Illuminate\\Process\\InvokedProcess',
        'aliasName' => NULL,
      ),
      'errorOutput' => 
      array (
        'name' => 'errorOutput',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the error output for the process.
 *
 * @return string
 */',
        'startLine' => 90,
        'endLine' => 93,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\InvokedProcess',
        'implementingClassName' => 'Illuminate\\Process\\InvokedProcess',
        'currentClassName' => 'Illuminate\\Process\\InvokedProcess',
        'aliasName' => NULL,
      ),
      'latestOutput' => 
      array (
        'name' => 'latestOutput',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the latest standard output for the process.
 *
 * @return string
 */',
        'startLine' => 100,
        'endLine' => 103,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\InvokedProcess',
        'implementingClassName' => 'Illuminate\\Process\\InvokedProcess',
        'currentClassName' => 'Illuminate\\Process\\InvokedProcess',
        'aliasName' => NULL,
      ),
      'latestErrorOutput' => 
      array (
        'name' => 'latestErrorOutput',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the latest error output for the process.
 *
 * @return string
 */',
        'startLine' => 110,
        'endLine' => 113,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\InvokedProcess',
        'implementingClassName' => 'Illuminate\\Process\\InvokedProcess',
        'currentClassName' => 'Illuminate\\Process\\InvokedProcess',
        'aliasName' => NULL,
      ),
      'wait' => 
      array (
        'name' => 'wait',
        'parameters' => 
        array (
          'output' => 
          array (
            'name' => 'output',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 123,
                'endLine' => 123,
                'startTokenPos' => 321,
                'startFilePos' => 2681,
                'endTokenPos' => 321,
                'endFilePos' => 2684,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'callable',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 123,
            'endLine' => 123,
            'startColumn' => 26,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Wait for the process to finish.
 *
 * @param  callable|null  $output
 * @return \\Illuminate\\Process\\ProcessResult
 *
 * @throws \\Illuminate\\Process\\Exceptions\\ProcessTimedOutException
 */',
        'startLine' => 123,
        'endLine' => 132,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\InvokedProcess',
        'implementingClassName' => 'Illuminate\\Process\\InvokedProcess',
        'currentClassName' => 'Illuminate\\Process\\InvokedProcess',
        'aliasName' => NULL,
      ),
      'waitUntil' => 
      array (
        'name' => 'waitUntil',
        'parameters' => 
        array (
          'output' => 
          array (
            'name' => 'output',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 142,
                'endLine' => 142,
                'startTokenPos' => 403,
                'startFilePos' => 3245,
                'endTokenPos' => 403,
                'endFilePos' => 3248,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'callable',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 142,
            'endLine' => 142,
            'startColumn' => 31,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Wait until the given callback returns true.
 *
 * @param  callable|null  $output
 * @return \\Illuminate\\Process\\ProcessResult
 *
 * @throws \\Illuminate\\Process\\Exceptions\\ProcessTimedOutException
 */',
        'startLine' => 142,
        'endLine' => 151,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\InvokedProcess',
        'implementingClassName' => 'Illuminate\\Process\\InvokedProcess',
        'currentClassName' => 'Illuminate\\Process\\InvokedProcess',
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