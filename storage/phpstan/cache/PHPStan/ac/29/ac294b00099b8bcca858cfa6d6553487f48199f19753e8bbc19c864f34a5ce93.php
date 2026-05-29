<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Support\PaymentGatewayRegistry.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Support\PaymentGatewayRegistry
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-90fcb0bf1635d6e87232b508e840bbd4c64f8cf4506e14ea79d732b3761235bc',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Support\\PaymentGatewayRegistry',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Support/PaymentGatewayRegistry.php',
      ),
    ),
    'namespace' => 'App\\Support',
    'name' => 'App\\Support\\PaymentGatewayRegistry',
    'shortName' => 'PaymentGatewayRegistry',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Gateways com driver implementado no código (PaymentGatewayManager).
 * Outros nomes não devem aparecer como configuráveis no admin.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 9,
    'endLine' => 33,
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
      'IMPLEMENTED' => 
      array (
        'declaringClassName' => 'App\\Support\\PaymentGatewayRegistry',
        'implementingClassName' => 'App\\Support\\PaymentGatewayRegistry',
        'name' => 'IMPLEMENTED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'mercadopago\', \'asaas\']',
          'attributes' => 
          array (
            'startLine' => 11,
            'endLine' => 11,
            'startTokenPos' => 25,
            'startFilePos' => 245,
            'endTokenPos' => 30,
            'endFilePos' => 268,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 11,
        'endLine' => 11,
        'startColumn' => 5,
        'endColumn' => 56,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'options' => 
      array (
        'name' => 'options',
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
 * @return array<string, string> slug => rótulo UI
 */',
        'startLine' => 16,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Support',
        'declaringClassName' => 'App\\Support\\PaymentGatewayRegistry',
        'implementingClassName' => 'App\\Support\\PaymentGatewayRegistry',
        'currentClassName' => 'App\\Support\\PaymentGatewayRegistry',
        'aliasName' => NULL,
      ),
      'isImplemented' => 
      array (
        'name' => 'isImplemented',
        'parameters' => 
        array (
          'gateway' => 
          array (
            'name' => 'gateway',
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
            'startLine' => 24,
            'endLine' => 24,
            'startColumn' => 42,
            'endColumn' => 56,
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
        'startLine' => 24,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Support',
        'declaringClassName' => 'App\\Support\\PaymentGatewayRegistry',
        'implementingClassName' => 'App\\Support\\PaymentGatewayRegistry',
        'currentClassName' => 'App\\Support\\PaymentGatewayRegistry',
        'aliasName' => NULL,
      ),
      'validationRule' => 
      array (
        'name' => 'validationRule',
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
        'startLine' => 29,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Support',
        'declaringClassName' => 'App\\Support\\PaymentGatewayRegistry',
        'implementingClassName' => 'App\\Support\\PaymentGatewayRegistry',
        'currentClassName' => 'App\\Support\\PaymentGatewayRegistry',
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