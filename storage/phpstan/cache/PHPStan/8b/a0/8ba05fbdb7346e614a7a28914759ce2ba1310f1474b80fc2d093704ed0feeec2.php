<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Jobs\OmniProcessMessage.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Jobs\OmniProcessMessage
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-cde7abc0d0f11fc72292e85ce0d2f1136a2b95972016b1eb9d39145ca20e0027',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Jobs\\OmniProcessMessage',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Jobs/OmniProcessMessage.php',
      ),
    ),
    'namespace' => 'App\\Jobs',
    'name' => 'App\\Jobs\\OmniProcessMessage',
    'shortName' => 'OmniProcessMessage',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 13,
    'endLine' => 36,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'Illuminate\\Contracts\\Queue\\ShouldQueue',
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Foundation\\Bus\\Dispatchable',
      1 => 'Illuminate\\Queue\\InteractsWithQueue',
      2 => 'Illuminate\\Bus\\Queueable',
      3 => 'Illuminate\\Queue\\SerializesModels',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'conversation' => 
      array (
        'declaringClassName' => 'App\\Jobs\\OmniProcessMessage',
        'implementingClassName' => 'App\\Jobs\\OmniProcessMessage',
        'name' => 'conversation',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 17,
        'endLine' => 17,
        'startColumn' => 5,
        'endColumn' => 28,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'content' => 
      array (
        'declaringClassName' => 'App\\Jobs\\OmniProcessMessage',
        'implementingClassName' => 'App\\Jobs\\OmniProcessMessage',
        'name' => 'content',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 18,
        'endLine' => 18,
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
          'conversation' => 
          array (
            'name' => 'conversation',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\OmniConversation',
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
            'startColumn' => 33,
            'endColumn' => 62,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'content' => 
          array (
            'name' => 'content',
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
            'startLine' => 23,
            'endLine' => 23,
            'startColumn' => 65,
            'endColumn' => 79,
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
 * Create a new job instance.
 */',
        'startLine' => 23,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Jobs',
        'declaringClassName' => 'App\\Jobs\\OmniProcessMessage',
        'implementingClassName' => 'App\\Jobs\\OmniProcessMessage',
        'currentClassName' => 'App\\Jobs\\OmniProcessMessage',
        'aliasName' => NULL,
      ),
      'handle' => 
      array (
        'name' => 'handle',
        'parameters' => 
        array (
          'service' => 
          array (
            'name' => 'service',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\OmniChatService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 32,
            'endLine' => 32,
            'startColumn' => 28,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Execute the job.
 */',
        'startLine' => 32,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Jobs',
        'declaringClassName' => 'App\\Jobs\\OmniProcessMessage',
        'implementingClassName' => 'App\\Jobs\\OmniProcessMessage',
        'currentClassName' => 'App\\Jobs\\OmniProcessMessage',
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