<?php declare(strict_types = 1);

// osfsl-C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../sentry/sentry-laravel/src/Sentry/Laravel/Console/PublishCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Sentry\Laravel\Console\PublishCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-b433b7fcd3053586200b53c242d11fd6d1394cefd1ed96fb6961e40e3fc45257-8.2.12-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Sentry\\Laravel\\Console\\PublishCommand',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../sentry/sentry-laravel/src/Sentry/Laravel/Console/PublishCommand.php',
      ),
    ),
    'namespace' => 'Sentry\\Laravel\\Console',
    'name' => 'Sentry\\Laravel\\Console\\PublishCommand',
    'shortName' => 'PublishCommand',
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
    'endLine' => 166,
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
        'declaringClassName' => 'Sentry\\Laravel\\Console\\PublishCommand',
        'implementingClassName' => 'Sentry\\Laravel\\Console\\PublishCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '<<<COMMAND
sentry:publish 
    { --dsn= : The DSN to configure }
    { --without-test : Do not send a test event }
    { --with-send-default-pii : Include information such as request headers, IP address and the authenticated user to events collected by the SDK }
    { --without-performance-monitoring : Do not enable performance monitoring }
    { --without-javascript-sdk : Do not enable the JavaScript SDK (deprecated; option unused) }
COMMAND',
          'attributes' => 
          array (
            'startLine' => 13,
            'endLine' => 20,
            'startTokenPos' => 48,
            'startFilePos' => 236,
            'endTokenPos' => 50,
            'endFilePos' => 681,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 13,
        'endLine' => 20,
        'startColumn' => 5,
        'endColumn' => 8,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'Sentry\\Laravel\\Console\\PublishCommand',
        'implementingClassName' => 'Sentry\\Laravel\\Console\\PublishCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Publishes and configures the Sentry config.\'',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 59,
            'startFilePos' => 714,
            'endTokenPos' => 59,
            'endFilePos' => 758,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
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
        'startLine' => 24,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Sentry\\Laravel\\Console',
        'declaringClassName' => 'Sentry\\Laravel\\Console\\PublishCommand',
        'implementingClassName' => 'Sentry\\Laravel\\Console\\PublishCommand',
        'currentClassName' => 'Sentry\\Laravel\\Console\\PublishCommand',
        'aliasName' => NULL,
      ),
      'setEnvValues' => 
      array (
        'name' => 'setEnvValues',
        'parameters' => 
        array (
          'values' => 
          array (
            'name' => 'values',
            'default' => NULL,
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
            'startLine' => 89,
            'endLine' => 89,
            'startColumn' => 35,
            'endColumn' => 47,
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
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 89,
        'endLine' => 126,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Sentry\\Laravel\\Console',
        'declaringClassName' => 'Sentry\\Laravel\\Console\\PublishCommand',
        'implementingClassName' => 'Sentry\\Laravel\\Console\\PublishCommand',
        'currentClassName' => 'Sentry\\Laravel\\Console\\PublishCommand',
        'aliasName' => NULL,
      ),
      'isEnvKeySet' => 
      array (
        'name' => 'isEnvKeySet',
        'parameters' => 
        array (
          'envKey' => 
          array (
            'name' => 'envKey',
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
            'startLine' => 128,
            'endLine' => 128,
            'startColumn' => 34,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'envFileContents' => 
          array (
            'name' => 'envFileContents',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 128,
                'endLine' => 128,
                'startTokenPos' => 752,
                'startFilePos' => 4392,
                'endTokenPos' => 752,
                'endFilePos' => 4395,
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
                      'name' => 'string',
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
            'startLine' => 128,
            'endLine' => 128,
            'startColumn' => 50,
            'endColumn' => 80,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
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
        'startLine' => 128,
        'endLine' => 133,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Sentry\\Laravel\\Console',
        'declaringClassName' => 'Sentry\\Laravel\\Console\\PublishCommand',
        'implementingClassName' => 'Sentry\\Laravel\\Console\\PublishCommand',
        'currentClassName' => 'Sentry\\Laravel\\Console\\PublishCommand',
        'aliasName' => NULL,
      ),
      'getEnvKeyPattern' => 
      array (
        'name' => 'getEnvKeyPattern',
        'parameters' => 
        array (
          'envKey' => 
          array (
            'name' => 'envKey',
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
            'startLine' => 135,
            'endLine' => 135,
            'startColumn' => 39,
            'endColumn' => 52,
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
        'startLine' => 135,
        'endLine' => 138,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Sentry\\Laravel\\Console',
        'declaringClassName' => 'Sentry\\Laravel\\Console\\PublishCommand',
        'implementingClassName' => 'Sentry\\Laravel\\Console\\PublishCommand',
        'currentClassName' => 'Sentry\\Laravel\\Console\\PublishCommand',
        'aliasName' => NULL,
      ),
      'askForDsnInput' => 
      array (
        'name' => 'askForDsnInput',
        'parameters' => 
        array (
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
        'startLine' => 140,
        'endLine' => 165,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Sentry\\Laravel\\Console',
        'declaringClassName' => 'Sentry\\Laravel\\Console\\PublishCommand',
        'implementingClassName' => 'Sentry\\Laravel\\Console\\PublishCommand',
        'currentClassName' => 'Sentry\\Laravel\\Console\\PublishCommand',
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