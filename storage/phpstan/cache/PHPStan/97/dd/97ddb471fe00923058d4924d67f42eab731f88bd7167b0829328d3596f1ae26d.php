<?php declare(strict_types = 1);

// osfsl-C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../laravel/framework/src/Illuminate/Notifications/Events/NotificationFailed.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Notifications\Events\NotificationFailed
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-ab67799eacc553a2015a22b63162389e17bbc6de17518997dadbcbe1d8565bbe-8.2.12-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Notifications\\Events\\NotificationFailed',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../laravel/framework/src/Illuminate/Notifications/Events/NotificationFailed.php',
      ),
    ),
    'namespace' => 'Illuminate\\Notifications\\Events',
    'name' => 'Illuminate\\Notifications\\Events\\NotificationFailed',
    'shortName' => 'NotificationFailed',
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
    'endLine' => 56,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Bus\\Queueable',
      1 => 'Illuminate\\Queue\\SerializesModels',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'notifiable' => 
      array (
        'declaringClassName' => 'Illuminate\\Notifications\\Events\\NotificationFailed',
        'implementingClassName' => 'Illuminate\\Notifications\\Events\\NotificationFailed',
        'name' => 'notifiable',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The notifiable entity who received the notification.
 *
 * @var mixed
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
      'notification' => 
      array (
        'declaringClassName' => 'Illuminate\\Notifications\\Events\\NotificationFailed',
        'implementingClassName' => 'Illuminate\\Notifications\\Events\\NotificationFailed',
        'name' => 'notification',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The notification instance.
 *
 * @var \\Illuminate\\Notifications\\Notification
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 25,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'channel' => 
      array (
        'declaringClassName' => 'Illuminate\\Notifications\\Events\\NotificationFailed',
        'implementingClassName' => 'Illuminate\\Notifications\\Events\\NotificationFailed',
        'name' => 'channel',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The channel name.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 20,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'data' => 
      array (
        'declaringClassName' => 'Illuminate\\Notifications\\Events\\NotificationFailed',
        'implementingClassName' => 'Illuminate\\Notifications\\Events\\NotificationFailed',
        'name' => 'data',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 38,
            'endLine' => 38,
            'startTokenPos' => 60,
            'startFilePos' => 644,
            'endTokenPos' => 61,
            'endFilePos' => 645,
          ),
        ),
        'docComment' => '/**
 * The data needed to process this failure.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 22,
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
          'notifiable' => 
          array (
            'name' => 'notifiable',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 49,
            'endLine' => 49,
            'startColumn' => 33,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'notification' => 
          array (
            'name' => 'notification',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 49,
            'endLine' => 49,
            'startColumn' => 46,
            'endColumn' => 58,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'channel' => 
          array (
            'name' => 'channel',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 49,
            'endLine' => 49,
            'startColumn' => 61,
            'endColumn' => 68,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'data' => 
          array (
            'name' => 'data',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 49,
                'endLine' => 49,
                'startTokenPos' => 85,
                'startFilePos' => 969,
                'endTokenPos' => 86,
                'endFilePos' => 970,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 49,
            'endLine' => 49,
            'startColumn' => 71,
            'endColumn' => 80,
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
 * Create a new event instance.
 *
 * @param  mixed  $notifiable
 * @param  \\Illuminate\\Notifications\\Notification  $notification
 * @param  string  $channel
 * @param  array  $data
 * @return void
 */',
        'startLine' => 49,
        'endLine' => 55,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Notifications\\Events',
        'declaringClassName' => 'Illuminate\\Notifications\\Events\\NotificationFailed',
        'implementingClassName' => 'Illuminate\\Notifications\\Events\\NotificationFailed',
        'currentClassName' => 'Illuminate\\Notifications\\Events\\NotificationFailed',
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