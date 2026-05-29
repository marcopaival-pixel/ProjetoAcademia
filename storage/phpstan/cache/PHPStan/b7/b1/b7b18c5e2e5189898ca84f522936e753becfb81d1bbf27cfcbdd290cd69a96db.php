<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Http\Controllers\Auth\VerificationController.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Http\Controllers\Auth\VerificationController
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-de77e09a3875f323c14e5f4eac1004cc6bc9427b1ba3b2d38b04f3f14bae33a1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Http/Controllers/Auth/VerificationController.php',
      ),
    ),
    'namespace' => 'App\\Http\\Controllers\\Auth',
    'name' => 'App\\Http\\Controllers\\Auth\\VerificationController',
    'shortName' => 'VerificationController',
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
    'endLine' => 149,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'App\\Http\\Controllers\\Controller',
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
      'verificationService' => 
      array (
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'name' => 'verificationService',
        'modifiers' => 2,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\EmailVerificationService',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 14,
        'endLine' => 14,
        'startColumn' => 9,
        'endColumn' => 63,
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
          'verificationService' => 
          array (
            'name' => 'verificationService',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\EmailVerificationService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 14,
            'endLine' => 14,
            'startColumn' => 9,
            'endColumn' => 63,
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
        'startLine' => 13,
        'endLine' => 15,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Auth',
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'currentClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'aliasName' => NULL,
      ),
      'show' => 
      array (
        'name' => 'show',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Exibe a página informando que o e-mail precisa ser confirmado.
 */',
        'startLine' => 20,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Auth',
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'currentClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'aliasName' => NULL,
      ),
      'verify' => 
      array (
        'name' => 'verify',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\Request',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 30,
            'endLine' => 30,
            'startColumn' => 28,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'token' => 
          array (
            'name' => 'token',
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
            'startLine' => 30,
            'endLine' => 30,
            'startColumn' => 46,
            'endColumn' => 58,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Processa a verificação do e-mail através do token (acesso público; o link pode ser aberto sem sessão).
 */',
        'startLine' => 30,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Auth',
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'currentClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'aliasName' => NULL,
      ),
      'success' => 
      array (
        'name' => 'success',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Página de sucesso após confirmação (utilizador autenticado).
 */',
        'startLine' => 55,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Auth',
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'currentClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'aliasName' => NULL,
      ),
      'failed' => 
      array (
        'name' => 'failed',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\Request',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 72,
            'endLine' => 72,
            'startColumn' => 28,
            'endColumn' => 43,
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
 * Token inválido ou expirado (público).
 */',
        'startLine' => 72,
        'endLine' => 79,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Auth',
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'currentClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'aliasName' => NULL,
      ),
      'resend' => 
      array (
        'name' => 'resend',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\Request',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 84,
            'endLine' => 84,
            'startColumn' => 28,
            'endColumn' => 43,
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
 * Reenvia o e-mail de confirmação (utilizador autenticado).
 */',
        'startLine' => 84,
        'endLine' => 122,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Auth',
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'currentClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'aliasName' => NULL,
      ),
      'resendGuest' => 
      array (
        'name' => 'resendGuest',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\Request',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 127,
            'endLine' => 127,
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
 * Reenvio para quem não tem sessão (ex.: login bloqueado por e-mail não confirmado).
 */',
        'startLine' => 127,
        'endLine' => 148,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Auth',
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
        'currentClassName' => 'App\\Http\\Controllers\\Auth\\VerificationController',
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