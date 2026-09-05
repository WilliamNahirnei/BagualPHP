# Plano de Testes — BagualPHP

Plano de testes automatizados para o framework, organizado por módulo. Ferramenta escolhida: **Codeception 4.x** (`^4.2`, compatível com PHP `>=7.4` exigido pelo `composer.json`), em substituição à proposta anterior de PHPUnit puro.

## Ferramenta e estrutura

- **Codeception `^4.2`** — usa PHPUnit por baixo (`Codeception\Test\Unit` estende `PHPUnit\Framework\TestCase`), então os desafios de testabilidade já mapeados continuam valendo (estado estático global em `Route`/`Response`/`ConfigLoader`, `Request` lendo superglobais no construtor — contornado via `newInstanceWithoutConstructor()` + Reflection até a refatoração do item 7 do `DIVIDA_TECNICA.md` existir).
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
- **`acceptance`** (módulo `PhpBrowser`): testa a aplicação de fora, via HTTP real, exigindo um servidor rodando (`php -S localhost:8000`). Faz sentido para testes de ponta a ponta do `Router` (ex.: bater numa rota real e conferir o vazamento de stack trace do item 5), mas é lento e desnecessário para verificar se `"motor"` vira `"mot"`.

Legenda da checklist: `[_bug]` = teste de caracterização que documenta um comportamento **atual e incorreto**, ligado a um item do `DIVIDA_TECNICA.md` (precisa ser revisado/atualizado quando o bug for corrigido). Sem marcação = teste de **comportamento esperado** (não é bug, é o funcionamento correto que deve ser preservado).

---

## 1. Router/Request — ✅ concluído

`tests/unit/Router/RequestTest.php` — cobre `Request::sanitizeParams()` (item 6 do `DIVIDA_TECNICA.md`).

- [x] `testIsolatedOrWordRemovedFromLegitimateText` `[_bug]` — `"Rafael or Silva"` → `"Rafael  Silva"`
- [x] `testWordEndingInOrGetsTruncated` `[_bug]` — `"motor"` → `"mot"`
- [x] `testCommonPtBrWordsEndingInOrAreTruncated` `[_bug]` — `"professor"` → `"profess"`, `"doutor"` → `"dout"`, `"administrador"` → `"administrad"`
- [x] `testEmailStartingWithReservedWordIsCorrupted` `[_bug]` — `"user@example.com"` → `"@example.com"`
- [x] `testActualSqlInjectionPayloadIsStillStripped` — `"1 OR 1=1"`, `"'; DROP TABLE users; --"` continuam neutralizados (comportamento esperado, protege contra regressão da correção futura)
- [x] `testNestedArraysAreSanitizedRecursively` — cobre a recursão em arrays aninhados (comportamento esperado)
- [x] `testGetAllMergedParamsMergesQueryAndBody` — comportamento esperado de `getAllMergedParams()`

Implementado em `tests/unit/Router/RequestTest.php`. Rodado com `vendor/bin/codecept run unit`: **7 testes, 14 assertions, OK**.

---

## 2. Routing/Endpoint (auth)

`tests/unit/Routing/EndpointAuthenticateTest.php` — cobre `Endpoint::authenticate()` (item 3 do `DIVIDA_TECNICA.md`).

- [ ] `testIgnoreAuthTrueSkipsAuthentication` — comportamento esperado
- [ ] `testValidAuthClassGrantsAccess` — comportamento esperado (`FakeAuth::authenticate()` retornando `true`)
- [ ] `testInvalidCredentialsThrowAuthenticationException` — comportamento esperado
- [ ] `testAuthClassNotExtendingAbstractAuthenticableThrowsException` — comportamento esperado (validação de `classExtends`)
- [ ] `testEndpointWithoutAnyAuthClassConfiguredAllowsAccess` `[_bug]` — `ignoreAuth = false` mas nenhuma `authClass`/`authMethod` resolvida (nem endpoint, nem módulo, nem global) ainda assim libera acesso (fail-open)

---

## 3. Routing/Controller

`tests/unit/Routing/EndpointControllerTest.php` — cobre `Endpoint::executeEndpoint()` / `validateExistenceEndpointExecutable()` (item 4 do `DIVIDA_TECNICA.md`), usando fixture `FakeController` com métodos `staticNoParams()`, `staticWithRequiredParam(string $id)`, `nonStaticUsingThis()`, `nonStaticNotUsingThis()`.

- [ ] `testStaticMethodWithoutParamsExecutesNormally` — comportamento esperado
- [ ] `testMissingControllerClassThrowsException` — comportamento esperado
- [ ] `testMissingControllerMethodThrowsException` — comportamento esperado
- [ ] `testNonStaticMethodUsingThisThrowsError` `[_bug]` — hoje gera `\Error` genérico (não uma falha de validação clara antes da chamada)
- [ ] `testNonStaticMethodNotUsingThisExecutesWithoutAnyError` `[_bug]` — o caso mais perigoso: nenhum erro é levantado, execução ocorre com semântica errada
- [ ] `testMethodWithRequiredParamThrowsArgumentCountError` `[_bug]` — deveria ser barrado antes da chamada por validação de assinatura, mas hoje só falha em runtime
- [ ] `testValidateExistenceEndpointExecutableNeverCallsMethodIsStaticOrMethodHasNoParameters` `[_bug]` — trava a ausência da validação; deve ser atualizado quando o item 4 for corrigido

---

## 4. Router/Response

`tests/unit/Router/ResponseTest.php` — cobre `Response` e `Router::generateInternalErrorMessage()` (item 5 do `DIVIDA_TECNICA.md`).

- [ ] `testMountCompleteResponseReturnsMessageAndDataKeys` — comportamento esperado
- [ ] `testGenerateServerResponseSetsHttpStatusCode` — comportamento esperado
- [ ] `testAddHeaderAccumulatesMultipleValuesForSameHeader` — comportamento esperado
- [ ] `testApiExceptionMessageDoesNotLeakStackTrace` — comportamento esperado (contraste com o item abaixo)
- [ ] `testInternalErrorResponseMessageContainsStackTrace` `[_bug]` — `defineInternalErrorResponse()` com um `\Throwable` genérico gera mensagem contendo `"Stack trace"` e caminho de arquivo, vazando informação interna no JSON de resposta

---

## 5. Autoloader

`tests/unit/AutoloaderTest.php` — cobre a inalcançabilidade de `Autoloader\Autoloader` (item 1 do `DIVIDA_TECNICA.md`).

- [ ] `testAutoloaderClassIsNotRegisteredViaSplAutoloadFunctions` `[_bug]` — nenhum callback registrado vem do namespace `Autoloader\` após o bootstrap padrão via Composer
- [ ] `testAutoloaderNamespaceIsAbsentFromComposerPsr4Map` `[_bug]` — `Autoloader\\` não consta em `composer.json` → `autoload.psr-4`

---

## Fora do escopo desta suíte

- **Item 2** (erros de grafia em métodos/namespaces): não é comportamento a verificar por teste automatizado — travar o typo num teste seria contraproducente. Tratado via *rename refactor* dedicado.
- **Item 7** (`Request` acoplado a superglobais / `RequestDataInterface`): a refatoração ainda não foi implementada (por decisão explícita). Assim que existir, adicionar aqui um novo bloco "Router/Request — RequestDataInterface" com testes que mockam a interface em vez de usar Reflection.
