<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Services\DompdfPdfService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\DompdfPdfService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-31b8ed423a94d1af21fb9630c48351473cfbe486484865ad0a8212e9c9f5df97',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\DompdfPdfService',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Services/DompdfPdfService.php',
      ),
    ),
    'namespace' => 'App\\Services',
    'name' => 'App\\Services\\DompdfPdfService',
    'shortName' => 'DompdfPdfService',
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
    'endLine' => 43,
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
    ),
    'immediateMethods' => 
    array (
      'render' => 
      array (
        'name' => 'render',
        'parameters' => 
        array (
          'html' => 
          array (
            'name' => 'html',
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
            'startLine' => 14,
            'endLine' => 14,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'paper' => 
          array (
            'name' => 'paper',
            'default' => 
            array (
              'code' => '\'A4\'',
              'attributes' => 
              array (
                'startLine' => 15,
                'endLine' => 15,
                'startTokenPos' => 43,
                'startFilePos' => 238,
                'endTokenPos' => 43,
                'endFilePos' => 241,
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
            'startLine' => 15,
            'endLine' => 15,
            'startColumn' => 9,
            'endColumn' => 28,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'orientation' => 
          array (
            'name' => 'orientation',
            'default' => 
            array (
              'code' => '\'portrait\'',
              'attributes' => 
              array (
                'startLine' => 16,
                'endLine' => 16,
                'startTokenPos' => 52,
                'startFilePos' => 274,
                'endTokenPos' => 52,
                'endFilePos' => 283,
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
            'startLine' => 16,
            'endLine' => 16,
            'startColumn' => 9,
            'endColumn' => 40,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'remoteEnabled' => 
          array (
            'name' => 'remoteEnabled',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 17,
                'endLine' => 17,
                'startTokenPos' => 61,
                'startFilePos' => 316,
                'endTokenPos' => 61,
                'endFilePos' => 320,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 17,
            'endLine' => 17,
            'startColumn' => 9,
            'endColumn' => 35,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
          'defaultFont' => 
          array (
            'name' => 'defaultFont',
            'default' => 
            array (
              'code' => '\'DejaVu Sans\'',
              'attributes' => 
              array (
                'startLine' => 18,
                'endLine' => 18,
                'startTokenPos' => 70,
                'startFilePos' => 353,
                'endTokenPos' => 70,
                'endFilePos' => 365,
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
            'startLine' => 18,
            'endLine' => 18,
            'startColumn' => 9,
            'endColumn' => 43,
            'parameterIndex' => 4,
            'isOptional' => true,
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
        'docComment' => '/**
 * @return non-empty-string Binary PDF content
 */',
        'startLine' => 13,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\DompdfPdfService',
        'implementingClassName' => 'App\\Services\\DompdfPdfService',
        'currentClassName' => 'App\\Services\\DompdfPdfService',
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