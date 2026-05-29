<?php declare(strict_types = 1);

// osfsl-C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../laravel/framework/src/Illuminate/Filesystem/LocalFilesystemAdapter.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Filesystem\LocalFilesystemAdapter
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-f8bd943cf4346ec3701d8cd869642f2d0910144418de7084c33553e5d620c2e4-8.2.12-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../laravel/framework/src/Illuminate/Filesystem/LocalFilesystemAdapter.php',
      ),
    ),
    'namespace' => 'Illuminate\\Filesystem',
    'name' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
    'shortName' => 'LocalFilesystemAdapter',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 9,
    'endLine' => 103,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Filesystem\\FilesystemAdapter',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Support\\Traits\\Conditionable',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'disk' => 
      array (
        'declaringClassName' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
        'implementingClassName' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
        'name' => 'disk',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The name of the filesystem disk.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 18,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 20,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'shouldServeSignedUrls' => 
      array (
        'declaringClassName' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
        'implementingClassName' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
        'name' => 'shouldServeSignedUrls',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 25,
            'endLine' => 25,
            'startTokenPos' => 52,
            'startFilePos' => 452,
            'endTokenPos' => 52,
            'endFilePos' => 456,
          ),
        ),
        'docComment' => '/**
 * Indicates if signed URLs should serve corresponding files.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 25,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 45,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'urlGeneratorResolver' => 
      array (
        'declaringClassName' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
        'implementingClassName' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
        'name' => 'urlGeneratorResolver',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The Closure that should be used to resolve the URL generator.
 *
 * @var \\Closure
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 36,
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
      'providesTemporaryUrls' => 
      array (
        'name' => 'providesTemporaryUrls',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if temporary URLs can be generated.
 *
 * @return bool
 */',
        'startLine' => 39,
        'endLine' => 44,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Filesystem',
        'declaringClassName' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
        'implementingClassName' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
        'currentClassName' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
        'aliasName' => NULL,
      ),
      'temporaryUrl' => 
      array (
        'name' => 'temporaryUrl',
        'parameters' => 
        array (
          'path' => 
          array (
            'name' => 'path',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 54,
            'endLine' => 54,
            'startColumn' => 34,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'expiration' => 
          array (
            'name' => 'expiration',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 54,
            'endLine' => 54,
            'startColumn' => 41,
            'endColumn' => 51,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 54,
                'endLine' => 54,
                'startTokenPos' => 123,
                'startFilePos' => 1195,
                'endTokenPos' => 124,
                'endFilePos' => 1196,
              ),
            ),
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
            'startLine' => 54,
            'endLine' => 54,
            'startColumn' => 54,
            'endColumn' => 72,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a temporary URL for the file at the given path.
 *
 * @param  string  $path
 * @param  \\DateTimeInterface  $expiration
 * @param  array  $options
 * @return string
 */',
        'startLine' => 54,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Filesystem',
        'declaringClassName' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
        'implementingClassName' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
        'currentClassName' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
        'aliasName' => NULL,
      ),
      'diskName' => 
      array (
        'name' => 'diskName',
        'parameters' => 
        array (
          'disk' => 
          array (
            'name' => 'disk',
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
            'startLine' => 82,
            'endLine' => 82,
            'startColumn' => 30,
            'endColumn' => 41,
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
 * Specify the name of the disk the adapter is managing.
 *
 * @param  string  $disk
 * @return $this
 */',
        'startLine' => 82,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Filesystem',
        'declaringClassName' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
        'implementingClassName' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
        'currentClassName' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
        'aliasName' => NULL,
      ),
      'shouldServeSignedUrls' => 
      array (
        'name' => 'shouldServeSignedUrls',
        'parameters' => 
        array (
          'serve' => 
          array (
            'name' => 'serve',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 96,
                'endLine' => 96,
                'startTokenPos' => 293,
                'startFilePos' => 2297,
                'endTokenPos' => 293,
                'endFilePos' => 2300,
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
            'startLine' => 96,
            'endLine' => 96,
            'startColumn' => 43,
            'endColumn' => 60,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'urlGeneratorResolver' => 
          array (
            'name' => 'urlGeneratorResolver',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 96,
                'endLine' => 96,
                'startTokenPos' => 303,
                'startFilePos' => 2336,
                'endTokenPos' => 303,
                'endFilePos' => 2339,
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
                      'name' => 'Closure',
                      'isIdentifier' => false,
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
            'startLine' => 96,
            'endLine' => 96,
            'startColumn' => 63,
            'endColumn' => 99,
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
 * Indiate that signed URLs should serve the corresponding files.
 *
 * @param  bool  $serve
 * @param  \\Closure|null  $urlGeneratorResolver
 * @return $this
 */',
        'startLine' => 96,
        'endLine' => 102,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Filesystem',
        'declaringClassName' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
        'implementingClassName' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
        'currentClassName' => 'Illuminate\\Filesystem\\LocalFilesystemAdapter',
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