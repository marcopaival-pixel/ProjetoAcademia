<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Http\Controllers\Patient\PortalController.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Http\Controllers\Patient\PortalController
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.12-9b63d084f475e6baf2d9aef74556240065845bcef91d619ef187c8f3ee318186',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'filename' => 'C:/Projetos/ProjetoAcademia/laravel-app/app/Http/Controllers/Patient/PortalController.php',
      ),
    ),
    'namespace' => 'App\\Http\\Controllers\\Patient',
    'name' => 'App\\Http\\Controllers\\Patient\\PortalController',
    'shortName' => 'PortalController',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 17,
    'endLine' => 486,
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
    ),
    'immediateMethods' => 
    array (
      'access' => 
      array (
        'name' => 'access',
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
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Valida o token de acesso e autentica o paciente.
 * Rota: /patient/access?token=xxx
 */',
        'startLine' => 23,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'aliasName' => NULL,
      ),
      'index' => 
      array (
        'name' => 'index',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\View\\View',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Dashboard Principal (Resumo)
 */',
        'startLine' => 71,
        'endLine' => 100,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'aliasName' => NULL,
      ),
      'treatmentPlan' => 
      array (
        'name' => 'treatmentPlan',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\View\\View',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Plano de Tratamento
 */',
        'startLine' => 105,
        'endLine' => 118,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'aliasName' => NULL,
      ),
      'evolution' => 
      array (
        'name' => 'evolution',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\View\\View',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Evolução do Paciente
 */',
        'startLine' => 123,
        'endLine' => 175,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'aliasName' => NULL,
      ),
      'prescriptions' => 
      array (
        'name' => 'prescriptions',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\View\\View',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Prescrições (Treinos e Dietas)
 */',
        'startLine' => 180,
        'endLine' => 193,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'aliasName' => NULL,
      ),
      'documents' => 
      array (
        'name' => 'documents',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\View\\View',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Documentos (Exames, Receitas, etc)
 */',
        'startLine' => 198,
        'endLine' => 209,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'aliasName' => NULL,
      ),
      'agenda' => 
      array (
        'name' => 'agenda',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\View\\View',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Agenda
 */',
        'startLine' => 214,
        'endLine' => 225,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'aliasName' => NULL,
      ),
      'medicalRecords' => 
      array (
        'name' => 'medicalRecords',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\View\\View',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Prontuário Médico (Portal do Paciente)
 */',
        'startLine' => 230,
        'endLine' => 248,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'aliasName' => NULL,
      ),
      'medicalEvolutions' => 
      array (
        'name' => 'medicalEvolutions',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\View\\View',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 250,
        'endLine' => 261,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'aliasName' => NULL,
      ),
      'medicalReports' => 
      array (
        'name' => 'medicalReports',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\View\\View',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 263,
        'endLine' => 274,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'aliasName' => NULL,
      ),
      'medicalPrescriptions' => 
      array (
        'name' => 'medicalPrescriptions',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\View\\View',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 276,
        'endLine' => 287,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'aliasName' => NULL,
      ),
      'medicalCertificates' => 
      array (
        'name' => 'medicalCertificates',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\View\\View',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 289,
        'endLine' => 300,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'aliasName' => NULL,
      ),
      'plans' => 
      array (
        'name' => 'plans',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\View\\View',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Exibe os planos disponíveis para o paciente se tornar aluno.
 */',
        'startLine' => 305,
        'endLine' => 382,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'aliasName' => NULL,
      ),
      'downloadReport' => 
      array (
        'name' => 'downloadReport',
        'parameters' => 
        array (
          'report' => 
          array (
            'name' => 'report',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\MedicalReport',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 384,
            'endLine' => 384,
            'startColumn' => 36,
            'endColumn' => 68,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'pdfService' => 
          array (
            'name' => 'pdfService',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\DompdfPdfService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 384,
            'endLine' => 384,
            'startColumn' => 71,
            'endColumn' => 112,
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
        'startLine' => 384,
        'endLine' => 391,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'aliasName' => NULL,
      ),
      'downloadCertificate' => 
      array (
        'name' => 'downloadCertificate',
        'parameters' => 
        array (
          'certificate' => 
          array (
            'name' => 'certificate',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\MedicalCertificate',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 393,
            'endLine' => 393,
            'startColumn' => 41,
            'endColumn' => 83,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'pdfService' => 
          array (
            'name' => 'pdfService',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\DompdfPdfService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 393,
            'endLine' => 393,
            'startColumn' => 86,
            'endColumn' => 127,
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
        'startLine' => 393,
        'endLine' => 400,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'aliasName' => NULL,
      ),
      'messages' => 
      array (
        'name' => 'messages',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\View\\View',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Mensagens e Avisos
 */',
        'startLine' => 405,
        'endLine' => 414,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'aliasName' => NULL,
      ),
      'exportLaudo' => 
      array (
        'name' => 'exportLaudo',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Exportar Laudo Próprio (Item 12)
 */',
        'startLine' => 419,
        'endLine' => 424,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'aliasName' => NULL,
      ),
      'accessLogs' => 
      array (
        'name' => 'accessLogs',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\View\\View',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Histórico de Acessos (Item 12 e 13)
 */',
        'startLine' => 429,
        'endLine' => 440,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'aliasName' => NULL,
      ),
      'downloadPrescription' => 
      array (
        'name' => 'downloadPrescription',
        'parameters' => 
        array (
          'prescription' => 
          array (
            'name' => 'prescription',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\MedicalPrescription',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 442,
            'endLine' => 442,
            'startColumn' => 42,
            'endColumn' => 86,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'pdfService' => 
          array (
            'name' => 'pdfService',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\DompdfPdfService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 442,
            'endLine' => 442,
            'startColumn' => 89,
            'endColumn' => 130,
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
        'startLine' => 442,
        'endLine' => 449,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'aliasName' => NULL,
      ),
      'getPatientContext' => 
      array (
        'name' => 'getPatientContext',
        'parameters' => 
        array (
          'patient' => 
          array (
            'name' => 'patient',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 454,
            'endLine' => 454,
            'startColumn' => 40,
            'endColumn' => 47,
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
 * Helper para buscar o contexto do paciente (Branding, Professional, Link)
 */',
        'startLine' => 454,
        'endLine' => 485,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Http\\Controllers\\Patient',
        'declaringClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'implementingClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
        'currentClassName' => 'App\\Http\\Controllers\\Patient\\PortalController',
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