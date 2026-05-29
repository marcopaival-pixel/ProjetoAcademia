<?php declare(strict_types = 1);

// odsl-C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../laravel/framework/src/Illuminate/Support/helpers.php-PHPStan\BetterReflection\Reflection\ReflectionFunction-retry
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-f2bb75414d49f9ea454dfd2ad3f08fabee873f523b69079bce1f427173da86f8',
   'data' => 
  array (
    'name' => 'retry',
    'parameters' => 
    array (
      'times' => 
      array (
        'name' => 'times',
        'default' => NULL,
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 321,
        'endLine' => 321,
        'startColumn' => 20,
        'endColumn' => 25,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'callback' => 
      array (
        'name' => 'callback',
        'default' => NULL,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'callable',
            'isIdentifier' => true,
          ),
        ),
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 321,
        'endLine' => 321,
        'startColumn' => 28,
        'endColumn' => 45,
        'parameterIndex' => 1,
        'isOptional' => false,
      ),
      'sleepMilliseconds' => 
      array (
        'name' => 'sleepMilliseconds',
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 321,
            'endLine' => 321,
            'startTokenPos' => 1330,
            'startFilePos' => 8014,
            'endTokenPos' => 1330,
            'endFilePos' => 8014,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 321,
        'endLine' => 321,
        'startColumn' => 48,
        'endColumn' => 69,
        'parameterIndex' => 2,
        'isOptional' => true,
      ),
      'when' => 
      array (
        'name' => 'when',
        'default' => 
        array (
          'code' => '\\null',
          'attributes' => 
          array (
            'startLine' => 321,
            'endLine' => 321,
            'startTokenPos' => 1337,
            'startFilePos' => 8025,
            'endTokenPos' => 1337,
            'endFilePos' => 8028,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 321,
        'endLine' => 321,
        'startColumn' => 72,
        'endColumn' => 83,
        'parameterIndex' => 3,
        'isOptional' => true,
      ),
    ),
    'returnsReference' => false,
    'returnType' => NULL,
    'attributes' => 
    array (
    ),
    'docComment' => '/**
 * Retry an operation a given number of times.
 *
 * @template TValue
 *
 * @param  int|array<int, int>  $times
 * @param  callable(int): TValue  $callback
 * @param  int|\\Closure(int, \\Throwable): int  $sleepMilliseconds
 * @param  (callable(\\Throwable): bool)|null  $when
 * @return TValue
 *
 * @throws \\Throwable
 */',
    'startLine' => 321,
    'endLine' => 352,
    'startColumn' => 5,
    'endColumn' => 5,
    'couldThrow' => false,
    'isClosure' => false,
    'isGenerator' => false,
    'isVariadic' => false,
    'isStatic' => false,
    'namespace' => NULL,
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'retry',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../laravel/framework/src/Illuminate/Support/helpers.php',
      ),
    ),
  ),
));