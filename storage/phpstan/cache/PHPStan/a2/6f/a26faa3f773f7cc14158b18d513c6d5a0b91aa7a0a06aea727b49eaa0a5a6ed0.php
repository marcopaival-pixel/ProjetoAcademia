<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Http\Middleware\SecurityHeaders.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Http\Middleware\SecurityHeaders
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-2285a6fd98b54ccccc468a6f87e24967b03691faa7a860779ecfcb306fa305d8',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Http\\Middleware\\SecurityHeaders',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Http/Middleware/SecurityHeaders.php',
      ),
    ),
    'namespace' => 'App\\Http\\Middleware',
    'name' => 'App\\Http\\Middleware\\SecurityHeaders',
    'shortName' => 'SecurityHeaders',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Injeta headers de segurança HTTP em todas as respostas da aplicação.
 *
 * Headers aplicados:
 *  - X-Frame-Options: bloqueia clickjacking (iframes de terceiros)
 *  - X-Content-Type-Options: impede MIME sniffing
 *  - Referrer-Policy: limita dados de referência enviados a terceiros
 *  - X-XSS-Protection: proteção extra em browsers legados
 *  - Permissions-Policy: desativa features de hardware não usadas
 *  - Content-Security-Policy: base restritiva — ajustar conforme CDN/assets externos
 *  - Strict-Transport-Security: força HTTPS (ativado apenas fora de local/testing)
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 21,
    'endLine' => 86,
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
      'handle' => 
      array (
        'name' => 'handle',
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
            'startLine' => 23,
            'endLine' => 23,
            'startColumn' => 28,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'next' => 
          array (
            'name' => 'next',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Closure',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 23,
            'endLine' => 23,
            'startColumn' => 46,
            'endColumn' => 58,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Symfony\\Component\\HttpFoundation\\Response',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 23,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Middleware',
        'declaringClassName' => 'App\\Http\\Middleware\\SecurityHeaders',
        'implementingClassName' => 'App\\Http\\Middleware\\SecurityHeaders',
        'currentClassName' => 'App\\Http\\Middleware\\SecurityHeaders',
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