<?php declare(strict_types = 1);

// osfsl-C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Queue\Console\WorkCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-f47c67a9965f44b37410cbcf61eb3f18d83666385f4ccfa13bb9c8fe028256e8-8.2.12-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/vendor/composer/../laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php',
      ),
    ),
    'namespace' => 'Illuminate\\Queue\\Console',
    'name' => 'Illuminate\\Queue\\Console\\WorkCommand',
    'shortName' => 'WorkCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
      0 => 
      array (
        'name' => 'Symfony\\Component\\Console\\Attribute\\AsCommand',
        'isRepeated' => false,
        'arguments' => 
        array (
          'name' => 
          array (
            'code' => '\'queue:work\'',
            'attributes' => 
            array (
              'startLine' => 23,
              'endLine' => 23,
              'startTokenPos' => 99,
              'startFilePos' => 674,
              'endTokenPos' => 99,
              'endFilePos' => 685,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 23,
    'endLine' => 387,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Console\\Command',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Support\\InteractsWithTime',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'signature' => 
      array (
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'queue:work
                            {connection? : The name of the queue connection to work}
                            {--name=default : The name of the worker}
                            {--queue= : The names of the queues to work}
                            {--daemon : Run the worker in daemon mode (Deprecated)}
                            {--once : Only process the next job on the queue}
                            {--stop-when-empty : Stop when the queue is empty}
                            {--delay=0 : The number of seconds to delay failed jobs (Deprecated)}
                            {--backoff=0 : The number of seconds to wait before retrying a job that encountered an uncaught exception}
                            {--max-jobs=0 : The number of jobs to process before stopping}
                            {--max-time=0 : The maximum number of seconds the worker should run}
                            {--force : Force the worker to run even in maintenance mode}
                            {--memory=128 : The memory limit in megabytes}
                            {--sleep=3 : Number of seconds to sleep when no job is available}
                            {--rest=0 : Number of seconds to rest between jobs}
                            {--timeout=60 : The number of seconds a child process can run}
                            {--tries=1 : Number of times to attempt a job before logging it failed}
                            {--json : Output the queue worker information as JSON}\'',
          'attributes' => 
          array (
            'startLine' => 33,
            'endLine' => 50,
            'startTokenPos' => 126,
            'startFilePos' => 855,
            'endTokenPos' => 126,
            'endFilePos' => 2368,
          ),
        ),
        'docComment' => '/**
 * The console command name.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 84,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Start processing jobs on the queue as a daemon\'',
          'attributes' => 
          array (
            'startLine' => 57,
            'endLine' => 57,
            'startTokenPos' => 137,
            'startFilePos' => 2483,
            'endTokenPos' => 137,
            'endFilePos' => 2530,
          ),
        ),
        'docComment' => '/**
 * The console command description.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 57,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 78,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'worker' => 
      array (
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'name' => 'worker',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The queue worker instance.
 *
 * @var \\Illuminate\\Queue\\Worker
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 64,
        'endLine' => 64,
        'startColumn' => 5,
        'endColumn' => 22,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'cache' => 
      array (
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'name' => 'cache',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The cache store implementation.
 *
 * @var \\Illuminate\\Contracts\\Cache\\Repository
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 71,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 21,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'latestStartedAt' => 
      array (
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'name' => 'latestStartedAt',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * Holds the start time of the last processed job, if any.
 *
 * @var float|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 78,
        'endLine' => 78,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'hasRegisteredListeners' => 
      array (
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'name' => 'hasRegisteredListeners',
        'modifiers' => 20,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 85,
            'endLine' => 85,
            'startTokenPos' => 171,
            'startFilePos' => 3086,
            'endTokenPos' => 171,
            'endFilePos' => 3090,
          ),
        ),
        'docComment' => '/**
 * Indicates if the worker\'s event listeners have been registered.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 85,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 51,
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
          'worker' => 
          array (
            'name' => 'worker',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Queue\\Worker',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 94,
            'endLine' => 94,
            'startColumn' => 33,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'cache' => 
          array (
            'name' => 'cache',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Contracts\\Cache\\Repository',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 94,
            'endLine' => 94,
            'startColumn' => 49,
            'endColumn' => 60,
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
 * Create a new queue work command.
 *
 * @param  \\Illuminate\\Queue\\Worker  $worker
 * @param  \\Illuminate\\Contracts\\Cache\\Repository  $cache
 * @return void
 */',
        'startLine' => 94,
        'endLine' => 100,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'aliasName' => NULL,
      ),
      'handle' => 
      array (
        'name' => 'handle',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Execute the console command.
 *
 * @return int|null
 */',
        'startLine' => 107,
        'endLine' => 135,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'aliasName' => NULL,
      ),
      'runWorker' => 
      array (
        'name' => 'runWorker',
        'parameters' => 
        array (
          'connection' => 
          array (
            'name' => 'connection',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 144,
            'endLine' => 144,
            'startColumn' => 34,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'queue' => 
          array (
            'name' => 'queue',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 144,
            'endLine' => 144,
            'startColumn' => 47,
            'endColumn' => 52,
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
 * Run the worker instance.
 *
 * @param  string  $connection
 * @param  string  $queue
 * @return int|null
 */',
        'startLine' => 144,
        'endLine' => 152,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'aliasName' => NULL,
      ),
      'gatherWorkerOptions' => 
      array (
        'name' => 'gatherWorkerOptions',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Gather all of the queue worker options as a single object.
 *
 * @return \\Illuminate\\Queue\\WorkerOptions
 */',
        'startLine' => 159,
        'endLine' => 174,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'aliasName' => NULL,
      ),
      'listenForEvents' => 
      array (
        'name' => 'listenForEvents',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Listen for the queue events in order to update the console output.
 *
 * @return void
 */',
        'startLine' => 181,
        'endLine' => 206,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'aliasName' => NULL,
      ),
      'writeOutput' => 
      array (
        'name' => 'writeOutput',
        'parameters' => 
        array (
          'job' => 
          array (
            'name' => 'job',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Contracts\\Queue\\Job',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 216,
            'endLine' => 216,
            'startColumn' => 36,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'status' => 
          array (
            'name' => 'status',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 216,
            'endLine' => 216,
            'startColumn' => 46,
            'endColumn' => 52,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'exception' => 
          array (
            'name' => 'exception',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 216,
                'endLine' => 216,
                'startTokenPos' => 838,
                'startFilePos' => 7207,
                'endTokenPos' => 838,
                'endFilePos' => 7210,
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
                      'name' => 'Throwable',
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
            'startLine' => 216,
            'endLine' => 216,
            'startColumn' => 55,
            'endColumn' => 82,
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
 * Write the status output for the queue worker for JSON or TTY.
 *
 * @param  Job  $job
 * @param  string  $status
 * @param  Throwable|null  $exception
 * @return void
 */',
        'startLine' => 216,
        'endLine' => 221,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'aliasName' => NULL,
      ),
      'writeOutputForCli' => 
      array (
        'name' => 'writeOutputForCli',
        'parameters' => 
        array (
          'job' => 
          array (
            'name' => 'job',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Contracts\\Queue\\Job',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 230,
            'endLine' => 230,
            'startColumn' => 42,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'status' => 
          array (
            'name' => 'status',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 230,
            'endLine' => 230,
            'startColumn' => 52,
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
 * Write the status output for the queue worker.
 *
 * @param  \\Illuminate\\Contracts\\Queue\\Job  $job
 * @param  string  $status
 * @return void
 */',
        'startLine' => 230,
        'endLine' => 267,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'aliasName' => NULL,
      ),
      'writeOutputAsJson' => 
      array (
        'name' => 'writeOutputAsJson',
        'parameters' => 
        array (
          'job' => 
          array (
            'name' => 'job',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Contracts\\Queue\\Job',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 277,
            'endLine' => 277,
            'startColumn' => 42,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'status' => 
          array (
            'name' => 'status',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 277,
            'endLine' => 277,
            'startColumn' => 52,
            'endColumn' => 58,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'exception' => 
          array (
            'name' => 'exception',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 277,
                'endLine' => 277,
                'startTokenPos' => 1269,
                'startFilePos' => 9345,
                'endTokenPos' => 1269,
                'endFilePos' => 9348,
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
                      'name' => 'Throwable',
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
            'startLine' => 277,
            'endLine' => 277,
            'startColumn' => 61,
            'endColumn' => 88,
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
 * Write the status output for the queue worker in JSON format.
 *
 * @param  \\Illuminate\\Contracts\\Queue\\Job  $job
 * @param  string  $status
 * @param  Throwable|null  $exception
 * @return void
 */',
        'startLine' => 277,
        'endLine' => 306,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'aliasName' => NULL,
      ),
      'now' => 
      array (
        'name' => 'now',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the current date / time.
 *
 * @return \\Illuminate\\Support\\Carbon
 */',
        'startLine' => 313,
        'endLine' => 323,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'aliasName' => NULL,
      ),
      'logFailedJob' => 
      array (
        'name' => 'logFailedJob',
        'parameters' => 
        array (
          'event' => 
          array (
            'name' => 'event',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Queue\\Events\\JobFailed',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 331,
            'endLine' => 331,
            'startColumn' => 37,
            'endColumn' => 52,
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
 * Store a failed job event.
 *
 * @param  \\Illuminate\\Queue\\Events\\JobFailed  $event
 * @return void
 */',
        'startLine' => 331,
        'endLine' => 339,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'aliasName' => NULL,
      ),
      'getQueue' => 
      array (
        'name' => 'getQueue',
        'parameters' => 
        array (
          'connection' => 
          array (
            'name' => 'connection',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 347,
            'endLine' => 347,
            'startColumn' => 33,
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
 * Get the queue name for the worker.
 *
 * @param  string  $connection
 * @return string
 */',
        'startLine' => 347,
        'endLine' => 352,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'aliasName' => NULL,
      ),
      'downForMaintenance' => 
      array (
        'name' => 'downForMaintenance',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the worker should run in maintenance mode.
 *
 * @return bool
 */',
        'startLine' => 359,
        'endLine' => 362,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'aliasName' => NULL,
      ),
      'outputUsingJson' => 
      array (
        'name' => 'outputUsingJson',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the worker should output using JSON.
 *
 * @return bool
 */',
        'startLine' => 369,
        'endLine' => 376,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'aliasName' => NULL,
      ),
      'flushState' => 
      array (
        'name' => 'flushState',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Reset static variables.
 *
 * @return void
 */',
        'startLine' => 383,
        'endLine' => 386,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\WorkCommand',
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