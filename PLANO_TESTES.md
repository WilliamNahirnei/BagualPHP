# Plano de Testes — BagualPHP

Plano de testes automatizados para o framework, organizado por módulo. Ferramenta escolhida: **Codeception 4.x** (`^4.2`, compatível com PHP `>=7.4` exigido pelo `composer.json`), em substituição à proposta anterior de PHPUnit puro.

## Ferramenta e estrutura

- **Codeception `^4.2`** — usa PHPUnit por baixo (`Codeception\Test\Unit` estende `PHPUnit\Framework\TestCase`), então os desafios de testabilidade já mapeados continuam valendo (estado estático global em `Route`/`Response`/`ConfigLoader`, `Request` lendo superglobais no construtor — contornado via `newInstanceWithoutConstructor()` + Reflection até a refatoração do item 6 do `DIVIDA_TECNICA.md` existir).
- Suíte usada para os testes deste plano: **`unit`** — testa as classes do framework isoladamente, no mesmo processo, sem servidor HTTP. As suítes `functional` e `acceptance` foram geradas pelo bootstrap padrão do Codeception, mas não são usadas por este plano (ver explicação abaixo).
- **Status: já instalado e inicializado.** `require-dev` em `composer.json`: `codeception/codeception: ^4.2`, `codeception/module-asserts: ^2.0` (a versão `3.x` exige PHP `^8.2`, incompatível com o `>=7.4` deste projeto), `codeception/module-phpbrowser: ^1.0.0` (adicionado automaticamente pelo `codecept bootstrap` para a suíte `acceptance`).
- Execução: `vendor/bin/codecept run unit`.

Estrutura real gerada por `composer require --dev codeception/codeception codeception/module-asserts` + `vendor/bin/codecept bootstrap`:
```
BagualPHP/
├── codeception.yml
└── tests/
    ├── unit.suite.yml
    ├── functional.suite.yml        (gerado pelo bootstrap, não usado neste plano)
    ├── acceptance.suite.yml        (gerado pelo bootstrap, não usado neste plano)
    ├── unit/                       (testes deste plano vão aqui)
    │   ├── Router/
    │   │   ├── RequestTest.php
    │   │   └── ResponseTest.php
    │   ├── Routing/
    │   │   ├── EndpointAuthenticateTest.php
    │   │   └── EndpointControllerTest.php
    │   ├── AutoloaderTest.php
    │   └── Fixtures/
    │       ├── Controllers/FakeController.php
    │       └── Auth/FakeAuth.php
    ├── functional/                 (vazio)
    ├── acceptance/                 (vazio)
    ├── _data/
    ├── _output/
    └── _support/
        ├── UnitTester.php, FunctionalTester.php, AcceptanceTester.php
        ├── Helper/{Unit,Functional,Acceptance}.php
        └── _generated/*TesterActions.php
```

### Por que a suíte `unit`, e não `functional`/`acceptance`

- **`unit`**: chama a classe diretamente no processo de teste (sem rede, sem servidor). É o nível certo para algo como `Request::sanitizeParams()` — um método `private` que só depende de um array de entrada e do `preg_replace` dos patterns, sem tocar em nada externo ao framework.
- **`functional`**: emula requisições dentro do mesmo processo via um módulo de framework (Symfony2, Laravel5 etc.). O BagualPHP não é um dos frameworks suportados nativamente pelo Codeception, então essa suíte ficaria sem módulo configurado a menos que se escreva um módulo customizado que instancie `Router` manualmente simulando superglobais — overhead desnecessário para testar um método isolado.
- **`acceptance`** (módulo `PhpBrowser`): testa a aplicação de fora, via HTTP real, exigindo um servidor rodando (`php -S localhost:8000`). Faz sentido para testes de ponta a ponta do `Router` (ex.: bater numa rota real e conferir o conteúdo de um erro 500 genérico), mas é lento e desnecessário para verificar se `"motor"` vira `"mot"`.

Legenda da checklist: `[_bug]` = teste ligado a um item do `DIVIDA_TECNICA.md` que já especifica o **comportamento correto esperado** (não o comportamento atual). Esses testes devem falhar contra o código de hoje — a falha é o que evidencia que o bug existe — e só passam quando o item correspondente da dívida técnica é de fato corrigido. Sem marcação = teste de **comportamento esperado** que já é o funcionamento correto atual (não é bug, deve ser preservado).

---

## 1. Router/Request — ✅ concluído

`tests/unit/Router/RequestTest.php` — cobre `Request::sanitizeParams()` (item 5 do `DIVIDA_TECNICA.md`).

**Atualização (item 5 resolvido por remoção)**: a chamada a `sanitizeParams()` foi removida do construtor de `Request` (o método permanece na classe, marcado `@deprecated`, só para os testes de caracterização abaixo continuarem exercitando o comportamento antigo isoladamente). Os 4 testes `[_bug]` continuam válidos como testes de caracterização do método `sanitizeParams()` em si (que ainda existe e ainda tem o bug de pattern na causa raiz). Foi adicionado um novo teste de comportamento esperado que confirma que o fluxo real (via `Request::getInstance()`) não sanitiza mais nada.

- [x] `testIsolatedOrWordRemovedFromLegitimateText` `[_bug]` — `"Rafael or Silva"` → `"Rafael  Silva"` (testa `sanitizeParams()` isolado via Reflection, método não é mais chamado no fluxo real)
- [x] `testWordEndingInOrGetsTruncated` `[_bug]` — `"motor"` → `"mot"` (idem)
- [x] `testCommonPtBrWordsEndingInOrAreTruncated` `[_bug]` — `"professor"` → `"profess"`, `"doutor"` → `"dout"`, `"administrador"` → `"administrad"` (idem)
- [x] `testEmailStartingWithReservedWordIsCorrupted` `[_bug]` — `"user@example.com"` → `"@example.com"` (idem)
- [x] `testActualSqlInjectionPayloadIsStillStripped` — `"1 OR 1=1"`, `"'; DROP TABLE users; --"` continuam neutralizados quando `sanitizeParams()` é chamado diretamente (comportamento esperado do método isolado)
- [x] `testNestedArraysAreSanitizedRecursively` — cobre a recursão em arrays aninhados (comportamento esperado do método isolado)
- [x] `testGetAllMergedParamsMergesQueryAndBody` — comportamento esperado de `getAllMergedParams()`
- [x] `testQueryParamsArriveUnalteredNowThatSanitizationWasRemovedFromTheFlow` — comportamento esperado **do fluxo real**: instancia `Request` via `getInstance()` (construtor real, sem Reflection para pular `sanitizeParams()`) com `$_GET` contendo os mesmos valores que antes eram corrompidos (`"Rafael or Silva"`, `"motor"`, `"admin@example.com"`, símbolos `--`/`#`/`;`/`*`) e confirma que `getQueryParams()` retorna tudo idêntico a `$_GET`, sem nenhuma alteração

Implementado em `tests/unit/Router/RequestTest.php`. Rodado com `vendor/bin/codecept run unit`: **8 testes, 15 assertions, OK**.

---

## 2. Routing/Endpoint (auth) — implementado (4 testes `[_bug]` falhando por design, item 3 da `DIVIDA_TECNICA.md` ainda "Aberto")

`tests/unit/Routing/EndpointAuthenticateTest.php` — cobre `Endpoint::authenticate()` (item 3 do `DIVIDA_TECNICA.md`). Fixtures: `tests/unit/Fixtures/Auth/FakeAuth.php`, `tests/unit/Fixtures/Auth/FakeAuthNotExtendingAbstract.php`.

- [x] `testIgnoreAuthTrueSkipsAuthentication` — comportamento esperado
- [x] `testValidAuthClassGrantsAccess` — comportamento esperado (`FakeAuth::authenticate()` retornando `true`)
- [x] `testInvalidCredentialsThrowAuthenticationException` — comportamento esperado
- [x] `testAuthClassNotExtendingAbstractAuthenticableThrowsException` — comportamento esperado (validação de `classExtends`)
- [x] `testAuthMethodNonStaticThrowsException` — comportamento esperado (validação de `methodIsStatic` em `authClassIAutenticatle()`, mesmo padrão já coberto para o controller no item 3)
- [x] `testAuthMethodWithParametersThrowsException` — comportamento esperado (validação de `methodHasNoParameters` em `authClassIAutenticatle()`, mesmo padrão já coberto para o controller no item 3)
- [x] `testMissingAuthClassThrowsException` — comportamento esperado (`authClass` aponta para classe inexistente; validação de `classExists` em `validateExistenceAuthenticationDefined()`)
- [x] `testMissingAuthMethodThrowsException` — comportamento esperado (`authMethod` inexistente na classe de auth; validação de `methodExists` em `validateExistenceAuthenticationDefined()`)
- [x] `testEndpointWithoutAnyAuthClassConfiguredThrowsConfigurationException` `[_bug]` — implementado especificando o comportamento **correto** (item 3 da `DIVIDA_TECNICA.md`, ainda "Aberto"). **Falha hoje** (`Failed asserting that exception of type "Exception" is thrown`) contra `Endpoint::authenticate()`, que retorna `true` (fail-open) — a falha é intencional e evidencia o bug; só passará quando o item 3 for corrigido.
- [x] `testAuthClassDefinedWithoutAuthMethodThrowsConfigurationException` `[_bug]` — mesmo espírito (a condição real é `empty($authClass) || empty($authMethod)`, um OU). **Falha hoje**, pelo mesmo motivo.
- [x] `testAuthMethodDefinedWithoutAuthClassThrowsConfigurationException` `[_bug]` — caso simétrico. **Falha hoje.**
- [x] `testLoadDefaultAuthAppPreservesCustomAuthMethodWhenAuthClassIsEmpty` `[_bug]` — comportamento correto: `loadDefaultAuthApp()` deveria preservar um `authMethod` customizado já definido, preenchendo só `authClass` a partir do global. **Falha hoje** (`Expected 'alternateAuthenticate' / Actual 'authenticate'`) porque `loadDefaultAuthApp()` sobrescreve os dois incondicionalmente (`Endpoint.php:231-234`).
- [x] `testGlobalDefaultAuthClassIsUsedWhenEndpointHasNoAuthClass` — comportamento esperado (fallback global com sucesso: `authClass` não passado no construtor do `Endpoint`, `AuthConfig::DEFAULT_CLASS_NAMESPACE` mockada via Reflection apontando para `FakeAuth::class`, autenticação ocorre de fato por esse caminho)
- [x] `testGlobalDefaultAuthClassIsUsedWhenEndpointHasNoAuthClassAndCredentialsAreInvalid` — comportamento esperado (mesmo fallback global, mas `FakeAuth::authenticate()` retorna `false` — separado do teste anterior em vez de um único teste cobrindo sucesso e falha, já que cada `expectException()` do PHPUnit encerra o método de teste)

**Configuração via `.env`**: os testes que dependem do fallback global (`AuthConfig::DEFAULT_CLASS_NAMESPACE`) não tocam o arquivo real `envsConfigs/.auth.env` nem escrevem nele. Em vez disso, usam Reflection para ler/sobrescrever a propriedade `protected $config` da instância singleton de `AuthConfig::getInstance()` em `_before()`/`_after()`, restaurando o valor original ao final de cada teste — o mesmo padrão de contorno de estado estático global já usado no projeto (`ConfigLoader`/`Route`/`Response`).

Rodado com `vendor/bin/codecept run unit` (suíte completa): **32 testes, 49 assertions, 4 falhas** — as 4 falhas são os testes `[_bug]` acima, falhando por design.

### 2b. Precedência endpoint > módulo (mesma funcionalidade de auth, classe diferente) — implementado

`Endpoint::authenticate()` só enxerga o que já chegou pronto no construtor (endpoint ou módulo, indistinguível) e o fallback global — a prioridade "endpoint sobrescreve módulo" é resolvida antes disso, em `AbstractApi::addEndpoint()` (`Server/Routing/AbstractApi.php:75-95`). Como é a mesma funcionalidade de autenticação (o segundo dos 3 níveis: endpoint → módulo → global), fica coberta aqui também, mas por exercitar `AbstractApi` (classe abstrata — precisa de uma fixture concreta que a estenda, `tests/unit/Fixtures/Api/FakeApi.php`) em vez de `Endpoint`, num arquivo de teste separado: `tests/unit/Routing/AbstractApiAuthTest.php`.

- [x] `testEndpointAuthClassOverridesModuleDefaultAuthClass` — comportamento esperado (`authClass`/`authMethod` passados em `addEndpoint()` prevalecem sobre `defaultAuthClass`/`defaultAuthMethod` do módulo — `$authClass ?? $this->defaultAuthClass`)
- [x] `testModuleDefaultAuthClassUsedWhenEndpointDoesNotSpecifyAuthClass` — comportamento esperado (endpoint não passa `authClass`/`authMethod`; usa o default do módulo)
- [x] `testEndpointIgnoreAuthFalseOverridesModuleIgnoreAuthTrue` — comportamento esperado (`ignoreAuth: false` passado explicitamente no endpoint prevalece sobre `ignoreAuth: true` do módulo — a checagem é `!== null`, não `??`, exatamente para permitir isso)
- [x] `testModuleIgnoreAuthUsedWhenEndpointDoesNotSpecifyIgnoreAuth` — comportamento esperado (endpoint não passa `ignoreAuth` (`null`); usa o default do módulo)

`FakeApi::callAddEndpoint()` grava o `Endpoint` resultante no estado estático de `Route` (mesmo efeito colateral de `AbstractApi::addEndpoint()` real); os testes leem de volta via `Route::fecthRouteList()`. `_after()` reseta `Route::$allRoutesRoutesFunctions` via Reflection para o array vazio padrão, evitando vazamento entre testes.

Rodado com `vendor/bin/codecept run unit` (suíte completa, mesma execução do item 2 acima): as 4 asserções deste item passaram.

---

## 3. Routing/Controller — ✅ concluído (item 4 da `DIVIDA_TECNICA.md` corrigido)

`tests/unit/Routing/EndpointControllerTest.php` — cobre `Endpoint::executeEndpoint()` / `validateExistenceEndpointExecutable()` (item 4 do `DIVIDA_TECNICA.md`), usando fixture `tests/unit/Fixtures/Controllers/FakeController.php` (`namespace Fixtures\Controllers;`) com métodos `staticNoParams()`, `staticWithRequiredParam(string $id)`, `nonStaticUsingThis()`, `nonStaticNotUsingThis()`. Endpoints são instanciados diretamente (`new Endpoint(...)`, construtor público) com `ignoreAuth = true`, isolando o módulo da resolução de autenticação (item 3).

- [x] `testStaticMethodWithoutParamsExecutesNormally` — comportamento esperado
- [x] `testMissingControllerClassThrowsException` — comportamento esperado
- [x] `testMissingControllerMethodThrowsException` — comportamento esperado
- [x] `testNonStaticControllerMethodIsRejectedBeforeExecution` — comportamento esperado (corrigido): método não-estático é barrado por `\Exception` clara antes da chamada, com a mensagem `"... must be static"`
- [x] `testNonStaticControllerMethodIsRejectedEvenWhenItDoesNotUseThis` — comportamento esperado (corrigido): mesmo o caso mais perigoso do bug original (método não-estático que não usa `$this` e antes executava silenciosamente) agora é barrado antes da execução
- [x] `testControllerMethodWithRequiredParamIsRejectedBeforeExecution` — comportamento esperado (corrigido): método com parâmetro obrigatório é barrado por `\Exception` clara (`"... must not have parameters"`) em vez de falhar em runtime com `ArgumentCountError`

**Atualização — os 3 testes `[_bug]` foram reescritos para especificar o comportamento correto**: o usuário observou que os testes `[_bug]` originais só atestavam a existência do bug (documentavam o erro atual), sem validar como a validação deveria se comportar corretamente. Os 3 testes foram reescritos para exigir que `validateExistenceEndpointExecutable()` rejeite explicitamente métodos não-estáticos ou com parâmetros — e `Endpoint::validateExistenceEndpointExecutable()` (`Server/Routing/Endpoint.php`) foi corrigido de fato, adicionando `methodIsStatic()` e `methodHasNoParameters()` (já existentes em `TraitSuportValidationClass`, reaproveitados do mesmo jeito que já eram usados para a classe/método de auth em `authClassIAutenticatle()`). O item 4 da `DIVIDA_TECNICA.md` está resolvido.

**Decisão anterior, ainda válida**: o sétimo teste cogitado (`testValidateExistenceEndpointExecutableNeverCallsMethodIsStaticOrMethodHasNoParameters`, baseado em inspeção de código-fonte via Reflection) continua descartado — não fazia sentido mesmo antes da correção, e faz ainda menos sentido agora que a validação existe de fato e é coberta comportamentalmente pelos 3 testes acima.

Foi necessário adicionar `autoload-dev` (`"psr-4": {"": "tests/unit/"}`) ao `composer.json` e rodar `composer dump-autoload`, já que `FakeController` (diferente das classes de `Server\`) não tinha nenhum mapeamento PSR-4 para ser carregada.

Implementado em `tests/unit/Routing/EndpointControllerTest.php`. Rodado com `vendor/bin/codecept run unit` (suíte completa): **14 testes, 24 assertions, OK**.

---

## 4. Router/Response — ✅ concluído

`tests/unit/Router/ResponseTest.php` — cobre a API própria de `Response` (`mountCompleteResponse()`, `generateServerResponse()`, `addHeader()`). Os 2 testes que exercitavam `Router::defineApiExceptionErrorResponse()`/`defineInternalErrorResponse()` foram movidos para o item 6 (`RouterRequestFlowTest.php`) — testavam o método público do `Router`, não a API de `Response`; mantê-los aqui só porque a fixture era conveniente prejudicava a coesão do arquivo (mesmo critério já aplicado no item 8 para `AbstractApi`/`Auth`).

- [x] `testMountCompleteResponseReturnsMessageAndDataKeys` — comportamento esperado
- [x] `testGenerateServerResponseSetsHttpStatusCode` — comportamento esperado
- [x] `testAddHeaderAccumulatesMultipleValuesForSameHeader` — comportamento esperado

**Estado estático entre testes**: `Response` guarda `$statusCode`, `$responseMessage` e `$headers` como propriedades `private static` (só `$responseContent` é de instância) — `new Response()` a cada teste não as reseta. `_before()`/`_after()` restauram `$statusCode`/`$responseMessage` para o default usando os próprios setters públicos (`Response::setStatusCode()`/`setResponseMessage()`); só `$headers` não tem setter/reset público (apenas `addHeader()`, que sempre acumula), então esse único caso usa um método privado do arquivo de teste (`resetHeaders()`/`headersProperty()`) que manipula a propriedade via Reflection.

Implementado em `tests/unit/Router/ResponseTest.php`. Rodado com `vendor/bin/codecept run unit Router/ResponseTest`: **3 testes, 3 assertions, OK**.

---

## 5. Config/ConfigLoader — ✅ concluído

`tests/unit/Config/ConfigLoaderTest.php` (novo) — cobre a base singleton de configuração usada por `RouterConfig`/`AuthConfig`, hoje exercitada só como efeito colateral de outras classes, sem nenhuma asserção própria sobre o parsing do `.env` ou o singleton por subclasse.

**Fixtures necessárias**: `ConfigLoader` é `abstract`, então precisa de subclasses concretas. Importante: `loadConfigsDirectoryPath()` resolve o diretório de configs de forma **fixa**, relativa à própria localização de `Config/ConfigLoader.php` (`realpath(__DIR__ . '/../') . '/envsConfigs'`) — não é possível apontar uma subclasse fixture para um diretório de fixtures em `tests/`. Por isso o arquivo `.env` de teste precisa existir de fato em `envsConfigs/` (ex.: `envsConfigs/.fake-config-loader-test.env`), versionado junto dos outros `.env` do projeto.

- `tests/unit/Fixtures/Config/FakeConfigLoader.php` — `FILE_NAME = '.fake-config-loader-test.env'`, `CONFIG_KEYS = ['ALLOWED_KEY', 'COMMENTED_KEY']`.
- `tests/unit/Fixtures/Config/FakeConfigLoaderSecondary.php` — mesma `FILE_NAME`, `CONFIG_KEYS` vazio; só existe para provar que o singleton é por classe concreta, não por arquivo.
- `tests/unit/Fixtures/Config/FakeConfigLoaderMissingFile.php` — `FILE_NAME` apontando para um arquivo que não existe.
- `envsConfigs/.fake-config-loader-test.env`:
  ```
  ALLOWED_KEY = valor-permitido
  # COMMENTED_KEY = nao-deveria-carregar
  NOT_IN_ALLOWLIST = deveria-ficar-de-fora
  ```

- [x] `testGetConfigReturnsValueLoadedFromEnvFile` — **faz/confronta**: lê `FakeConfigLoader::getInstance()->getConfig('ALLOWED_KEY')` e compara com o valor gravado no `.env` fixture. **Esperado**: retorna `"valor-permitido"` (com espaços das bordas já removidos por `trim()`).
- [x] `testGetConfigReturnsNullForKeyNotInAllowlist` — **faz/confronta**: `getConfig('NOT_IN_ALLOWLIST')`, chave presente no arquivo mas fora de `CONFIG_KEYS`. **Esperado**: `null` — `CONFIG_KEYS` funciona como allowlist, chaves fora dela nunca chegam a `$config`.
- [x] `testCommentedLineIsNotLoadedEvenIfKeyIsInAllowlist` — **faz/confronta**: `getConfig('COMMENTED_KEY')`, chave presente em `CONFIG_KEYS` mas cuja linha no arquivo começa com `#`. **Esperado**: `null` — linhas comentadas são ignoradas independentemente do allowlist.
- [x] `testGetConfigReturnsNullForKeyAbsentFromFile` — **faz/confronta**: `getConfig('CHAVE_INEXISTENTE')`, chave que não aparece em nenhuma linha do arquivo. **Esperado**: `null` (fallback `??` de `getConfig()`).
- [x] `testGetInstanceReturnsSameInstanceOnRepeatedCalls` — **faz/confronta**: duas chamadas a `FakeConfigLoader::getInstance()`. **Esperado**: `assertSame` — mesmo objeto (singleton).
- [x] `testGetInstanceReturnsDifferentInstancesForDifferentSubclasses` — **faz/confronta**: `FakeConfigLoader::getInstance()` vs `FakeConfigLoaderSecondary::getInstance()`. **Esperado**: `assertNotSame` — o cache (`self::$instances[$class]`) é por classe concreta, não compartilhado entre subclasses.
- [x] `testMissingConfigFileThrowsException` — **faz/confronta**: `FakeConfigLoaderMissingFile::getInstance()`, cujo `FILE_NAME` não existe em `envsConfigs/`. **Esperado**: `\Exception` lançada por `loadConfig()`, com mensagem citando o caminho do arquivo.

**Reset de estado entre testes**: `self::$instances` é `private static` e compartilhado entre todas as subclasses de `ConfigLoader` (inclusive `RouterConfig`/`AuthConfig` usadas por outros testes) — resetado só para as chaves das 3 fixtures deste arquivo em `_after()` (`resetInstance(string $class)`, Reflection sobre a propriedade `instances`), sem tocar nas entradas de `RouterConfig`/`AuthConfig` usadas pelos demais itens do plano.

Implementado em `tests/unit/Config/ConfigLoaderTest.php` + fixtures em `tests/unit/Fixtures/Config/` + `envsConfigs/.fake-config-loader-test.env`. Rodado com `vendor/bin/codecept run unit Config/ConfigLoaderTest`: **7 testes, 7 assertions, OK**.

---

## 6. Router — pipeline de roteamento (`executeRequest()` de ponta a ponta) — ✅ concluído

`tests/unit/Router/RouterRequestFlowTest.php` (novo) — cobre `executeRequest()`, `validadePrefixRoute()`, `invalidPrefixRoute()`, `getPrefixInRoute()`, `defineRequestData()`, `defineTreatedRoute()`, `processRequest()`, `apiRouteIsDefined()`, `callControllerMethodRoute()` — hoje **nenhum destes é chamado por nenhum teste existente** — e também os métodos públicos de tratamento de erro `defineApiExceptionErrorResponse()`/`defineInternalErrorResponse()` (movidos de `ResponseTest.php`, ver item 4: testam o `Router`, não a API própria de `Response`).

- [x] `testApiExceptionMessageDoesNotLeakStackTrace` — **faz/confronta**: `new Router()`, `defineApiExceptionErrorResponse()` com uma `ApiException`. **Esperado**: `Response::getResponseMessage()` igual à mensagem da exceção, sem `"Stack trace"` (contraste com o teste abaixo).
- [x] `testInternalErrorResponseMessageContainsStackTrace` — **faz/confronta**: `new Router()`, `defineInternalErrorResponse()` com um `\Exception` genérico. **Esperado**: mensagem contém `ServerMessage::INTERNAL_SERVER_ERRO`, `"Stack trace"` e o caminho do arquivo — comportamento intencional (ver PHPDoc de `generateInternalErrorMessage()`).

**Pré-requisitos de ambiente para estes testes**:
- Registrar rotas fixture diretamente via `Route::get()/post()/put()/delete()` com `Endpoint`s construídos sobre `FakeController` (mesma fixture do item 3), e resetar `Route::$allRoutesRoutesFunctions` em `_after()` (mesmo padrão do `AbstractApiAuthTest`).
- Setar/restaurar `$_SERVER['REQUEST_URI']`, `$_SERVER['PATH_INFO']` e `$_SERVER['REQUEST_METHOD']` por teste (a URI usada para validar o prefixo vem de `REQUEST_URI`, mas o matching de rota em si usa `PATH_INFO` — são lidos em pontos diferentes do fluxo).
- Resetar o singleton de `Request` entre testes (mesmo helper já usado em `RequestTest`).
- **Achado desta análise**: `processRequest()` sempre instancia um `ApiManager` e chama `loadApiEndpoints()`, que escaneia `Src/Modules/*` (`Server/Routing/ApiManager.php`). Como este repositório não tem `Src/Modules` (é um framework/skeleton, ver `CLAUDE.md`), `realpath()` retorna `false`, e `glob(false . '/*', GLOB_ONLYDIR)` vira `glob('/*', GLOB_ONLYDIR)` — varre os diretórios da **raiz do sistema de arquivos** em vez de not-op. Isso não quebra o teste (nenhum diretório de raiz costuma ter `Api.php`), mas é lento e indesejável; ver item 9 para o teste desse comportamento e a sugestão de criar `Src/Modules/.gitkeep`.
- Para simular `PREFIX_API` configurado (o `.router.env` real do projeto vem vazio), usar Reflection sobre `RouterConfig::getInstance()` (propriedade `protected $config`), mesmo padrão já usado para `AuthConfig` no item 2.

- [x] `testValidPrefixDoesNotThrowWhenPrefixApiIsConfigured` — **faz/confronta**: `PREFIX_API` mockado para `"api"`, `$_SERVER['REQUEST_URI'] = '/api/qualquer-coisa'`, chama `executeRequest()`. **Esperado**: nenhuma `ApiException` de prefixo — o fluxo segue adiante.
- [x] `testInvalidPrefixThrowsNotFoundWhenPrefixApiIsConfigured` — **faz/confronta**: `PREFIX_API` mockado para `"api"`, `$_SERVER['REQUEST_URI'] = '/outro-prefixo/x'`, chama `executeRequest()`. **Esperado**: resposta final com `Response::getStatusCode() === StatusCodes::HTTP_NOT_FOUND` e mensagem contendo `"api Not Found"` (o prefixo esperado + `ServerMessage::NOT_FOUND`).
- [x] `testAnyPrefixIsAcceptedWhenPrefixApiIsEmpty` — **faz/confronta**: `PREFIX_API` vazio (config padrão do projeto), qualquer `REQUEST_URI`. **Esperado**: a validação de prefixo é pulada (`invalidPrefixRoute()` retorna `false` sempre que `PREFIX_API` é vazio), fluxo segue sem 404 de prefixo.
- [x] `testPrefixIsStrippedFromRouteBeforeMatching` — **faz/confronta**: `PREFIX_API` mockado para `"api"`, `$_SERVER['PATH_INFO'] = '/api/users'`, rota fixture registrada em `/users`. **Esperado**: a rota é encontrada (o prefixo `/api` é removido antes do matching, via `defineTreatedRoute()`).
- [x] `testRegisteredRouteExecutesControllerAndSetsResponseContent` — **faz/confronta**: rota `GET /fake` registrada apontando para `FakeController::staticNoParams`, requisição `GET /fake`, chama `executeRequest()`. **Esperado**: `Response::getStatusCode()` continua o default (200) e o corpo JSON ecoado (capturado via `ob_start()`/`ob_get_clean()` em torno de `executeRequest()`) tem `data` igual a `"executed"`.
- [x] `testUnregisteredRouteThrowsNotFoundApiException` — **faz/confronta**: requisição para uma URI/método sem nenhuma rota registrada. **Esperado**: resposta final com `HTTP_NOT_FOUND` e mensagem contendo `"Route Not Found"` (`ServerMessage::ROUTE` + `ServerMessage::NOT_FOUND`).
- [x] `testOptionsRequestSucceedsWhenMatchingNonGetRouteExists` — **faz/confronta**: rota `POST /fake` registrada, requisição `OPTIONS /fake`. **Esperado**: `executeRequest()` não lança exceção e não altera o conteúdo da resposta (preflight CORS aceito, `apiRouteIsDefined()` retorna `true` pelo ramo `OPTIONS`).
- [x] `testOptionsRequestThrowsNotFoundWhenNoNonGetRouteExists` — **faz/confronta**: requisição `OPTIONS` para uma URI sem nenhuma rota `POST`/`PUT`/`DELETE` registrada. **Esperado**: `HTTP_NOT_FOUND`, mesmo comportamento do teste `testUnregisteredRouteThrowsNotFoundApiException`.
- [x] `testUnhandledThrowableFromControllerFallsBackToInternalErrorResponse` — **faz/confronta**: rota registrada para um controller fixture que lança um `\Exception` genérico (novo método em `FakeController`, ex. `staticThrowsException()`), requisição correspondente. **Esperado**: o `catch (\Throwable $e)` de `executeRequest()` identifica que não é `ApiException` e direciona para `defineInternalErrorResponse()` — `Response::getResponseMessage()` contém `ServerMessage::INTERNAL_SERVER_ERRO` e `"Stack trace"` (mesmo comportamento intencional do item 4, aqui provando que o `catch` do fluxo real escolhe o ramo certo, não repetindo as asserções detalhadas já feitas lá).

Implementado em `tests/unit/Router/RouterRequestFlowTest.php`. `_before()`/`_after()` fazem backup/restauração de `$_SERVER`, `$_GET` e do `$config` real de `RouterConfig`, além de resetar `Route`, o singleton de `Request` e o estado estático de `Response` — o mesmo cuidado de isolamento já usado nos itens 2, 2b e 4. Rodado com `vendor/bin/codecept run unit Router/RouterRequestFlowTest`: **11 testes, 19 assertions, OK**.

---

## 7. Routing/Route — `post()`/`put()`/`delete()` — ✅ concluído

`tests/unit/Routing/RouteTest.php` (novo) — `get()` e `fecthRouteList()` já são exercitados indiretamente pelo item 2b; `post()`, `put()` e `delete()` nunca são chamados por nenhum teste hoje.

- [x] `testPostRegistersEndpointUnderPostMethod` — **faz/confronta**: `Route::post($endpoint)`, depois lê `Route::fecthRouteList()['POST'][$endpoint->getEndpoint()]`. **Esperado**: é o mesmo objeto `$endpoint` (`assertSame`).
- [x] `testPutRegistersEndpointUnderPutMethod` — **faz/confronta**: idem, para `Route::put()`/`'PUT'`. **Esperado**: idem.
- [x] `testDeleteRegistersEndpointUnderDeleteMethod` — **faz/confronta**: idem, para `Route::delete()`/`'DELETE'`. **Esperado**: idem.

`_after()` reseta `Route::$allRoutesRoutesFunctions` via Reflection (mesmo padrão do `AbstractApiAuthTest`).

Implementado em `tests/unit/Routing/RouteTest.php`. Rodado com `vendor/bin/codecept run unit Routing/RouteTest`: **3 testes, 3 assertions, OK**.

---

## 8. Routing/AbstractApi — `defineEndpointUri()` (casos não cobertos pelo item 2b) — ✅ concluído

`tests/unit/Routing/AbstractApiEndpointUriTest.php` (novo, separado de `AbstractApiAuthTest.php` — concatenação de URI não é uma questão de autenticação, misturar os dois no mesmo arquivo prejudicava a coesão). O caso "`moduleName` e `endpoint` juntos" já é exercitado incidentalmente pelos testes de auth do item 2b (ex.: `/module-1/override`), mas só como chave do array, sem assertar o formato da concatenação em si — por isso ganhou um teste explícito próprio aqui também, para deixar as 4 combinações possíveis todas cobertas de forma explícita. Os testes aqui não dependem de `FakeAuth` (passam `null` para `defaultAuthClass`/`defaultAuthMethod`), já que nenhum deles chama `executeEndpoint()`.

- [x] `testEndpointUriWithModuleNameAndEndpointConcatenatesBothSegments` — **faz/confronta**: `FakeApi('module-y', ...)`, `callAddEndpoint('GET', ..., 'both-defined')`. **Esperado**: rota registrada em `/module-y/both-defined` (os dois segmentos concatenados).
- [x] `testEndpointUriWithoutModuleNameOmitsModuleSegment` — **faz/confronta**: `FakeApi(null, ...)` (sem `moduleName`), `callAddEndpoint('GET', ..., 'only-endpoint')`. **Esperado**: rota registrada em `/only-endpoint` (sem segmento de módulo).
- [x] `testEndpointUriWithoutEndpointPathOmitsEndpointSegment` — **faz/confronta**: `FakeApi('module-x', ...)`, `callAddEndpoint('GET', ..., null)` (endpoint `null`). **Esperado**: rota registrada em `/module-x` (sem segundo segmento).
- [x] `testEndpointUriWithoutModuleNameOrEndpointIsEmptyString` — **faz/confronta**: `FakeApi(null, ...)`, `callAddEndpoint('GET', ..., null)`. **Esperado**: rota registrada na chave `""` (string vazia) — nenhum segmento adicionado.

Implementado em `tests/unit/Routing/AbstractApiEndpointUriTest.php` (reaproveitando `FakeApi`/`FakeController`, com seu próprio `resetRoutes()`). Rodado com `vendor/bin/codecept run unit Routing/AbstractApiEndpointUriTest`: **4 testes, 4 assertions, OK**.

---

## 9. Routing/ApiManager — ✅ concluído

`tests/unit/Routing/ApiManagerTest.php` (novo) — `loadApiEndpoints()` e `getNamespaceFromPath()` nunca são exercitados hoje.

**Fixture dinâmica, não commitada**: `MODULES_PATH` é uma constante privada fixa (`Src/Modules`, relativa à raiz do projeto) — não é possível injetar um diretório de teste alternativo. Os testes criam a estrutura de arquivos em `_before()` (`mkdir`/`file_put_contents`) e apagam tudo em `_after()` (`unlink`/`rmdir`), para não deixar um módulo fixture commitado em `Src/Modules/` (este repositório, por ser o framework/skeleton, não deve ter código de aplicação em `Src/`, ver `CLAUDE.md`).

- [x] `testLoadApiEndpointsCallsDefineEndpointListOfEachModuleFound` — **faz/confronta**: cria `Src/Modules/FakeTestModule/Api.php` (namespace `Src\Modules\FakeTestModule`, classe `Api` com `defineEndpointList()` público que marca uma flag estática como chamada), roda `(new ApiManager())->loadApiEndpoints()`. **Esperado**: a flag foi marcada — confirma que `getNamespaceFromPath()` resolveu o namespace certo e que a classe/método foram de fato instanciados/chamados.
- [x] `testLoadApiEndpointsSkipsModuleDirectoryWithoutApiFile` — **faz/confronta**: cria `Src/Modules/EmptyModule/` sem nenhum `Api.php`, roda `loadApiEndpoints()`. **Esperado**: nenhuma exceção lançada (o `glob()` interno de arquivos `Api.php` simplesmente não encontra nada nesse diretório).

**Achado desta análise, já corrigido**: quando `Src/Modules` não existe, `realpath(__DIR__ . self::MODULES_PATH)` retorna `false`; a concatenação `false . '/*'` vira a string `'/*'`, e `glob('/*', GLOB_ONLYDIR)` acabava varrendo a **raiz do sistema de arquivos** em vez de simplesmente não encontrar módulos. Não quebrava nada funcionalmente, mas era um efeito colateral não intencional (custo de I/O desnecessário, comportamento surpreendente para quem lê o código). Corrigido versionando `Src/Modules/.gitkeep`, garantindo que o diretório sempre exista (confirmado com `php -r`: `loadApiEndpoints()` roda em ~0.0001s em vez de varrer `/`).

Implementado em `tests/unit/Routing/ApiManagerTest.php` — `_after()` apaga `Src/Modules/FakeTestModule/` e `Src/Modules/EmptyModule/`, deixando só o `.gitkeep`. Rodado com `vendor/bin/codecept run unit Routing/ApiManagerTest`: **2 testes, 2 assertions, OK**.

---

## 10. Router/Request — getters restantes (`getBodyParams`, `getHeaders`, `getMethod`, `getUri`, `getFiles`) — ✅ concluído

Adicionar a `tests/unit/Router/RequestTest.php`. Todos seguem o mesmo padrão já usado em `testGetAllMergedParamsMergesQueryAndBody`: instanciar via `makeRequestWithoutConstructor()` (contorna o acoplamento a superglobais do construtor, item 6 da `DIVIDA_TECNICA.md`) e usar `setPrivateProperty()` para popular o campo antes de ler pelo getter público — são getters simples, sem lógica além de retornar a propriedade.

- [x] `testGetBodyParamsReturnsStoredBodyParams` — **faz/confronta**: seta `bodyParams` via Reflection, chama `getBodyParams()`. **Esperado**: retorna exatamente o array setado.
- [x] `testGetHeadersReturnsStoredHeaders` — **faz/confronta**: idem, para `headers`. **Esperado**: idem.
- [x] `testGetMethodReturnsStoredMethod` — **faz/confronta**: idem, para `method` (string). **Esperado**: idem.
- [x] `testGetUriReturnsStoredUri` — **faz/confronta**: idem, para `uri` (string). **Esperado**: idem.
- [x] `testGetFilesReturnsStoredFiles` — **faz/confronta**: idem, para `files`. **Esperado**: idem.

Implementado em `tests/unit/Router/RequestTest.php`. Rodado com `vendor/bin/codecept run unit Router/RequestTest`: **13 testes, 20 assertions, OK**.

---

## 11. Errors/ApiException — getters e valores default nunca exercitados — ✅ concluído

`tests/unit/Errors/ApiExceptionTest.php` (novo) — `getAccept()`, `getType()`, `getErrorListMessage()` e `getRestCode()` hoje não são chamados em nenhum lugar do código (nem produção, nem teste); só o construtor é exercitado indiretamente.

- [x] `testGetAcceptReturnsTrueWhenConstructedWithTrue` — **faz/confronta**: `new ApiException(true, ...)`, chama `getAccept()`. **Esperado**: `true`.
- [x] `testGetAcceptReturnsFalseWhenConstructedWithFalse` — **faz/confronta**: `new ApiException(false, ...)`, chama `getAccept()`. **Esperado**: `false`.
- [x] `testGetAcceptDefaultsToTrueWhenNotProvided` — **faz/confronta**: `new ApiException()` sem argumentos, chama `getAccept()`. **Esperado**: `true` (default do construtor).
- [x] `testGetTypeReturnsConstructorValue` — **faz/confronta**: `new ApiException(true, 'warning', ...)`, chama `getType()`. **Esperado**: `'warning'`.
- [x] `testGetTypeDefaultsToErrorTypeWhenNotProvided` — **faz/confronta**: `new ApiException()`, chama `getType()`. **Esperado**: `ApiExceptionTypes::ERROR`.
- [x] `testGetErrorListMessageReturnsImplodedMessages` — **faz/confronta**: `new ApiException(true, ApiExceptionTypes::ERROR, ['a', 'b'])`, chama `getErrorListMessage()`. **Esperado**: `"a|b"` (implode com `"|"`, igual ao que `getMessage()` herdado de `\Exception` também retorna).
- [x] `testGetRestCodeReturnsConstructorValue` — **faz/confronta**: `new ApiException(true, ApiExceptionTypes::ERROR, [], StatusCodes::HTTP_BAD_REQUEST)`, chama `getRestCode()`. **Esperado**: `400`.
- [x] `testGetRestCodeDefaultsToInternalServerErrorWhenNotProvided` — **faz/confronta**: `new ApiException()`, chama `getRestCode()`. **Esperado**: `StatusCodes::HTTP_INTERNAL_SERVER_ERROR` (500).

Implementado em `tests/unit/Errors/ApiExceptionTest.php`. Rodado com `vendor/bin/codecept run unit Errors/ApiExceptionTest`: **8 testes, 8 assertions, OK**.

---

## 12. Errors/AuthenticationException — defaults e customização — ✅ concluído

`tests/unit/Errors/AuthenticationExceptionTest.php` (novo) — hoje só se testa que a exceção **é lançada** (item 2); nenhum teste confere sua mensagem/código.

- [x] `testDefaultMessageIsUnauthorizedAuthenticationError` — **faz/confronta**: `new AuthenticationException()`, chama `getMessage()` (herdado de `ApiException`/`\Exception`). **Esperado**: igual a `ServerMessage::DEFAULT_AUTH_ERROR`.
- [x] `testDefaultRestCodeIsHttpUnauthorized` — **faz/confronta**: `new AuthenticationException()`, chama `getCode()`. **Esperado**: `StatusCodes::HTTP_UNAUTHORIZED` (401).
- [x] `testCustomMessageAndCodeOverrideDefaults` — **faz/confronta**: `new AuthenticationException(['erro customizado'], StatusCodes::HTTP_FORBIDDEN)`. **Esperado**: `getMessage() === 'erro customizado'` e `getCode() === 403`.

Implementado em `tests/unit/Errors/AuthenticationExceptionTest.php`. Rodado com `vendor/bin/codecept run unit Errors/AuthenticationExceptionTest`: **3 testes, 3 assertions, OK**.

---

## 13. Cobertura de branches adicionais — ✅ concluído

Revisão método a método de toda a suíte (78 testes) para achar cenários/branches ainda sem teste dentro de métodos **já** cobertos (não classes inteiras sem teste, como nos itens 5-12, mas um `if`/caminho específico dentro de um método testado). 8 achados, cada um num arquivo escolhido pelo comportamento exercitado (não por conveniência de fixture, mesmo critério do item 8/51).

**`AbstractApi::addEndpoint()` — dispatch dinâmico `Route::{$requestType}()`** (`tests/unit/Routing/AbstractApiMethodDispatchTest.php`, novo): até aqui, todo teste de `addEndpoint()` usava `'GET'`; o dispatch para `Route::post()/put()/delete()` nunca era exercitado através do `AbstractApi` (só diretamente em `Route`, item 7).
- [x] `testAddEndpointRegistersUnderPostWhenRequestTypeIsPost` — **faz/confronta**: `callAddEndpoint('POST', ...)`. **Esperado**: rota aparece em `Route::fecthRouteList()['POST']`.
- [x] `testAddEndpointRegistersUnderPutWhenRequestTypeIsPut` — **faz/confronta**: idem, `'PUT'`. **Esperado**: idem, em `['PUT']`.
- [x] `testAddEndpointRegistersUnderDeleteWhenRequestTypeIsDelete` — **faz/confronta**: idem, `'DELETE'`. **Esperado**: idem, em `['DELETE']`.

**`ApiManager::loadApiEndpoints()` — branches internos de `class_exists()`/`method_exists()`** (adicionado a `tests/unit/Routing/ApiManagerTest.php`): só o caminho de sucesso e "sem `Api.php`" eram testados.
- [x] `testLoadApiEndpointsSkipsModuleWhenApiFileDoesNotDeclareExpectedClass` — **faz/confronta**: `Api.php` existe mas declara `NotApi` em vez de `Api`. **Esperado**: `class_exists()` falso, nada é instanciado/chamado.
- [x] `testLoadApiEndpointsSkipsModuleWhenApiClassHasNoDefineEndpointListMethod` — **faz/confronta**: classe `Api` existe mas sem `defineEndpointList()`. **Esperado**: `method_exists()` falso, nenhuma exceção.

**`Router` — dispatch de método além de GET, e o OR de `apiRouteIsDefined()` no ramo `OPTIONS`** (adicionado a `tests/unit/Router/RouterRequestFlowTest.php`): o pipeline completo só tinha sido exercitado com `GET`, e o `OPTIONS` só contra uma rota `POST`.
- [x] `testRegisteredNonGetRouteExecutesControllerAndSetsResponseContent` — **faz/confronta**: rota `POST` registrada e requisitada via `executeRequest()`. **Esperado**: `HTTP_OK`, `data` igual a `"executed"` — mesmo comportamento do GET, agora provado para outro método.
- [x] `testOptionsRequestSucceedsWhenMatchingPutRouteExists` — **faz/confronta**: `OPTIONS` com só uma rota `PUT` registrada para a URI. **Esperado**: `HTTP_OK` (ramo independente do `||` em `apiRouteIsDefined()`).
- [x] `testOptionsRequestSucceedsWhenMatchingDeleteRouteExists` — **faz/confronta**: idem, com `DELETE`. **Esperado**: idem.

**`Request::sanitizeParams()` e construtor real** (adicionado a `tests/unit/Router/RequestTest.php`):
- [x] `testNonStringNonArrayValuesPassThroughUnchanged` — **faz/confronta**: `sanitizeParams()` com `int`/`bool`/`null`/`float` no array. **Esperado**: todos passam intactos (o método só trata `string`/`array`).
- [x] `testMethodAndUriArePopulatedFromServerSuperglobalsThroughRealConstructor` — **faz/confronta**: `Request::getInstance()` real (não via Reflection) com `$_SERVER['REQUEST_METHOD']`/`REQUEST_URI` setados. **Esperado**: `getMethod()`/`getUri()` refletem exatamente esses valores (só `getQueryParams()` era conferido pelo construtor real até então; `getBodyParams()`/`getHeaders()`/`getFiles()` continuam só no teste isolado via Reflection, por dependerem de `php://input`/`$_FILES`, difíceis de popular em CLI).

**`Response` — estado default e efeito colateral de `json_encode($dados, true)`** (adicionado a `tests/unit/Router/ResponseTest.php`):
- [x] `testNewResponseHasDefaultStatusMessageAndContent` — **faz/confronta**: `new Response()` sem nenhum setter chamado. **Esperado**: `getStatusCode()`/`getResponseMessage()`/`getResponseContent()` iguais aos defaults da interface (`DEFAULT_STATUS_CODE`/`DEFAULT_MESSAGE`/`DEFAULT_CONTENT`).
- [x] `testGenerateServerResponseEscapesAngleBracketsInMessage` — **faz/confronta**: mensagem contendo `<script>...</script>`, chama `generateServerResponse()`. **Esperado** (comportamento atual, característica não documentada antes): o JSON gerado não contém `<script>` literal — `generateServerResponse()` chama `json_encode($dados, true)`, e o segundo argumento (`$flags`) recebe `true`, que o PHP converte para `1` (`JSON_HEX_TAG`), escapando `<`/`>` para `<`/`>`.

**Fora do escopo desta revisão** (achados descartados por não serem observáveis/valerem o esforço): o corpo do `foreach` de `Response::defineHeaders()` que efetivamente chama `header()` — confirmado empiricamente que `headers_list()` retorna vazio em CLI mesmo após `header()`, então esse branch não é verificável sem subir um servidor real; combinações parciais dos parâmetros default do construtor de `AuthenticationException`; e o branch de arquivo `.env` ilegível por permissão no `ConfigLoader` (frágil/dependente de ambiente).

Suíte completa rodada 3 vezes com `--seed` aleatório diferente: `vendor/bin/codecept run unit` — **90 testes, 126 assertions, 4 falhas** (as mesmas `[_bug]` do item 2, fail-open, item 3 da `DIVIDA_TECNICA.md` ainda "Aberto") em todas as 3 execuções.

---

## Fora do escopo desta suíte

- **Item 2** (erros de grafia em métodos/namespaces): não é comportamento a verificar por teste automatizado — travar o typo num teste seria contraproducente. Tratado via *rename refactor* dedicado.
- **Item 6** (`Request` acoplado a superglobais / `RequestDataInterface`): a refatoração ainda não foi implementada (por decisão explícita). Assim que existir, adicionar aqui um novo bloco "Router/Request — RequestDataInterface" com testes que mockam a interface em vez de usar Reflection.
- **`Autoloader/Autoloader.php`** (item 1 da `DIVIDA_TECNICA.md`, código morto/inalcançável): removido do plano a pedido explícito do usuário; travar sua inalcançabilidade num teste ficaria no mesmo espírito descartado para o item 2 acima (inspecionar ausência de uso, não comportamento).
- **`AbstractAuthenticable::callAuthError()`**: método de contrato que hoje **nenhum ponto do core chama** — `Endpoint::authenticate()` lança `AuthenticationException` diretamente, sem passar por `callAuthError()`. Escrever um teste travando essa ausência de chamada seria inspecionar código-fonte, não comportamento observável; fica só como observação (possível inconsistência entre o contrato documentado em `CLAUDE.md` e o que o core realmente usa).
- **`Endpoint::getRequestType()`**: implementado, mas nunca chamado em lugar nenhum do core (nem em produção, nem em teste) — mesmo raciocínio acima, não é candidato a teste automatizado, só a uma limpeza futura se confirmado que é código morto.
