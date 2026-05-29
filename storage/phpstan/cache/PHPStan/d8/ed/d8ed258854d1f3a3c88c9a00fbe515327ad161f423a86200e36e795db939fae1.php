<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Http\Controllers\Admin\ClinicImplantationController.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Http\Controllers\Admin\ClinicImplantationController
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-61ec5f9c1ae9b3537005169f2125520b29b6739d82a5342e1df37f143f09c2d8',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Http/Controllers/Admin/ClinicImplantationController.php',
      ),
    ),
    'namespace' => 'App\\Http\\Controllers\\Admin',
    'name' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
    'shortName' => 'ClinicImplantationController',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 15,
    'endLine' => 274,
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
      'steps' => 
      array (
        'declaringClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'name' => 'steps',
        'modifiers' => 4,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[1 => [\'key\' => \'registration\', \'title\' => \'Cadastro da clínica\'], 2 => [\'key\' => \'plan\', \'title\' => \'Seleção do plano\'], 3 => [\'key\' => \'initial_config\', \'title\' => \'Configuração visual\'], 4 => [\'key\' => \'users\', \'title\' => \'Equipe administrativa\'], 5 => [\'key\' => \'specialties\', \'title\' => \'Especialidades\'], 6 => [\'key\' => \'professionals\', \'title\' => \'Corpo clínico\'], 7 => [\'key\' => \'agenda\', \'title\' => \'Configuração da agenda\'], 8 => [\'key\' => \'patients\', \'title\' => \'Banco de pacientes\'], 9 => [\'key\' => \'medical_records\', \'title\' => \'Configuração do prontuário\'], 10 => [\'key\' => \'training\', \'title\' => \'Treinamento\'], 11 => [\'key\' => \'test\', \'title\' => \'Homologação\'], 12 => [\'key\' => \'activation\', \'title\' => \'Ativação final\']]',
          'attributes' => 
          array (
            'startLine' => 17,
            'endLine' => 30,
            'startTokenPos' => 68,
            'startFilePos' => 414,
            'endTokenPos' => 310,
            'endFilePos' => 1268,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 17,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 6,
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
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 32,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Admin',
        'declaringClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'currentClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'aliasName' => NULL,
      ),
      'index' => 
      array (
        'name' => 'index',
        'parameters' => 
        array (
          'company' => 
          array (
            'name' => 'company',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 37,
                'endLine' => 37,
                'startTokenPos' => 350,
                'startFilePos' => 1417,
                'endTokenPos' => 350,
                'endFilePos' => 1420,
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
                      'name' => 'App\\Models\\AcademyCompany',
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
            'startLine' => 37,
            'endLine' => 37,
            'startColumn' => 27,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 37,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Admin',
        'declaringClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'currentClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'aliasName' => NULL,
      ),
      'showStep' => 
      array (
        'name' => 'showStep',
        'parameters' => 
        array (
          'company' => 
          array (
            'name' => 'company',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\AcademyCompany',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 52,
            'endLine' => 52,
            'startColumn' => 30,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'step' => 
          array (
            'name' => 'step',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 52,
            'endLine' => 52,
            'startColumn' => 55,
            'endColumn' => 59,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 52,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Admin',
        'declaringClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'currentClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'aliasName' => NULL,
      ),
      'saveStep' => 
      array (
        'name' => 'saveStep',
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
            'startLine' => 76,
            'endLine' => 76,
            'startColumn' => 30,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'company' => 
          array (
            'name' => 'company',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\AcademyCompany',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 76,
            'endLine' => 76,
            'startColumn' => 48,
            'endColumn' => 70,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'step' => 
          array (
            'name' => 'step',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 76,
            'endLine' => 76,
            'startColumn' => 73,
            'endColumn' => 77,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 76,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Admin',
        'declaringClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'currentClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'aliasName' => NULL,
      ),
      'prepareStepData' => 
      array (
        'name' => 'prepareStepData',
        'parameters' => 
        array (
          'company' => 
          array (
            'name' => 'company',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\AcademyCompany',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 88,
            'endLine' => 88,
            'startColumn' => 38,
            'endColumn' => 60,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'step' => 
          array (
            'name' => 'step',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 88,
            'endLine' => 88,
            'startColumn' => 63,
            'endColumn' => 67,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 88,
        'endLine' => 129,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Http\\Controllers\\Admin',
        'declaringClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'currentClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'aliasName' => NULL,
      ),
      'completeStep' => 
      array (
        'name' => 'completeStep',
        'parameters' => 
        array (
          'company' => 
          array (
            'name' => 'company',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\AcademyCompany',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 131,
            'endLine' => 131,
            'startColumn' => 35,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'stepNumber' => 
          array (
            'name' => 'stepNumber',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 131,
            'endLine' => 131,
            'startColumn' => 60,
            'endColumn' => 70,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'data' => 
          array (
            'name' => 'data',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 131,
                'endLine' => 131,
                'startTokenPos' => 1290,
                'startFilePos' => 5649,
                'endTokenPos' => 1290,
                'endFilePos' => 5652,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 131,
            'endLine' => 131,
            'startColumn' => 73,
            'endColumn' => 84,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 131,
        'endLine' => 153,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Http\\Controllers\\Admin',
        'declaringClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'currentClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'aliasName' => NULL,
      ),
      'saveStep1' => 
      array (
        'name' => 'saveStep1',
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
            'startLine' => 157,
            'endLine' => 157,
            'startColumn' => 32,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'company' => 
          array (
            'name' => 'company',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\AcademyCompany',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 157,
            'endLine' => 157,
            'startColumn' => 50,
            'endColumn' => 72,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 157,
        'endLine' => 170,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Http\\Controllers\\Admin',
        'declaringClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'currentClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'aliasName' => NULL,
      ),
      'saveStep2' => 
      array (
        'name' => 'saveStep2',
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
            'startLine' => 172,
            'endLine' => 172,
            'startColumn' => 32,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'company' => 
          array (
            'name' => 'company',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\AcademyCompany',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 172,
            'endLine' => 172,
            'startColumn' => 50,
            'endColumn' => 72,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 172,
        'endLine' => 187,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Http\\Controllers\\Admin',
        'declaringClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'currentClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'aliasName' => NULL,
      ),
      'saveStep3' => 
      array (
        'name' => 'saveStep3',
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
            'startLine' => 189,
            'endLine' => 189,
            'startColumn' => 32,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'company' => 
          array (
            'name' => 'company',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\AcademyCompany',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 189,
            'endLine' => 189,
            'startColumn' => 50,
            'endColumn' => 72,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 189,
        'endLine' => 211,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Http\\Controllers\\Admin',
        'declaringClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'currentClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'aliasName' => NULL,
      ),
      'saveStep4' => 
      array (
        'name' => 'saveStep4',
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
            'startLine' => 213,
            'endLine' => 213,
            'startColumn' => 32,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'company' => 
          array (
            'name' => 'company',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\AcademyCompany',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 213,
            'endLine' => 213,
            'startColumn' => 50,
            'endColumn' => 72,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 213,
        'endLine' => 223,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Http\\Controllers\\Admin',
        'declaringClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'currentClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'aliasName' => NULL,
      ),
      'saveStep5' => 
      array (
        'name' => 'saveStep5',
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
            'startLine' => 225,
            'endLine' => 225,
            'startColumn' => 32,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'company' => 
          array (
            'name' => 'company',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\AcademyCompany',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 225,
            'endLine' => 225,
            'startColumn' => 50,
            'endColumn' => 72,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 225,
        'endLine' => 233,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Http\\Controllers\\Admin',
        'declaringClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'currentClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'aliasName' => NULL,
      ),
      'saveStep6' => 
      array (
        'name' => 'saveStep6',
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
            'startLine' => 235,
            'endLine' => 235,
            'startColumn' => 32,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'company' => 
          array (
            'name' => 'company',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\AcademyCompany',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 235,
            'endLine' => 235,
            'startColumn' => 50,
            'endColumn' => 72,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 235,
        'endLine' => 242,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Http\\Controllers\\Admin',
        'declaringClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'currentClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'aliasName' => NULL,
      ),
      'saveStep7' => 
      array (
        'name' => 'saveStep7',
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
            'startLine' => 244,
            'endLine' => 244,
            'startColumn' => 32,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'company' => 
          array (
            'name' => 'company',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\AcademyCompany',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 244,
            'endLine' => 244,
            'startColumn' => 50,
            'endColumn' => 72,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 244,
        'endLine' => 247,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Http\\Controllers\\Admin',
        'declaringClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'currentClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'aliasName' => NULL,
      ),
      'saveStep8' => 
      array (
        'name' => 'saveStep8',
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
            'startLine' => 249,
            'endLine' => 249,
            'startColumn' => 32,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'company' => 
          array (
            'name' => 'company',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\AcademyCompany',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 249,
            'endLine' => 249,
            'startColumn' => 50,
            'endColumn' => 72,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 249,
        'endLine' => 252,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Http\\Controllers\\Admin',
        'declaringClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'currentClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'aliasName' => NULL,
      ),
      'saveStep9' => 
      array (
        'name' => 'saveStep9',
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
            'startLine' => 254,
            'endLine' => 254,
            'startColumn' => 32,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'company' => 
          array (
            'name' => 'company',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\AcademyCompany',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 254,
            'endLine' => 254,
            'startColumn' => 50,
            'endColumn' => 72,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 254,
        'endLine' => 258,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Http\\Controllers\\Admin',
        'declaringClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'currentClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'aliasName' => NULL,
      ),
      'saveStep10' => 
      array (
        'name' => 'saveStep10',
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
            'startLine' => 260,
            'endLine' => 260,
            'startColumn' => 33,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'company' => 
          array (
            'name' => 'company',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\AcademyCompany',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 260,
            'endLine' => 260,
            'startColumn' => 51,
            'endColumn' => 73,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 260,
        'endLine' => 263,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Http\\Controllers\\Admin',
        'declaringClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'currentClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'aliasName' => NULL,
      ),
      'saveStep11' => 
      array (
        'name' => 'saveStep11',
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
            'startLine' => 265,
            'endLine' => 265,
            'startColumn' => 33,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'company' => 
          array (
            'name' => 'company',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\AcademyCompany',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 265,
            'endLine' => 265,
            'startColumn' => 51,
            'endColumn' => 73,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 265,
        'endLine' => 268,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Http\\Controllers\\Admin',
        'declaringClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'currentClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'aliasName' => NULL,
      ),
      'saveStep12' => 
      array (
        'name' => 'saveStep12',
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
            'startLine' => 270,
            'endLine' => 270,
            'startColumn' => 33,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'company' => 
          array (
            'name' => 'company',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\AcademyCompany',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 270,
            'endLine' => 270,
            'startColumn' => 51,
            'endColumn' => 73,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 270,
        'endLine' => 273,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Http\\Controllers\\Admin',
        'declaringClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'implementingClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
        'currentClassName' => 'App\\Http\\Controllers\\Admin\\ClinicImplantationController',
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