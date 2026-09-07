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

**Atualização (item 6 resolvido por remoção)**: a chamada a `sanitizeParams()` foi removida do construtor de `Request` (o método permanece na classe, marcado `@deprecated`, só para os testes de caracterização abaixo continuarem exercitando o comportamento antigo isoladamente). Os 4 testes `[_bug]` continuam válidos como testes de caracterização do método `sanitizeParams()` em si (que ainda existe e ainda tem o bug de pattern na causa raiz). Foi adicionado um novo teste de comportamento esperado que confirma que o fluxo real (via `Request::getInstance()`) não sanitiza mais nada.

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

## 2. Routing/Endpoint (auth)

`tests/unit/Routing/EndpointAuthenticateTest.php` — cobre `Endpoint::authenticate()` (item 3 do `DIVIDA_TECNICA.md`).

- [ ] `testIgnoreAuthTrueSkipsAuthentication` — comportamento esperado
- [ ] `testValidAuthClassGrantsAccess` — comportamento esperado (`FakeAuth::authenticate()` retornando `true`)
- [ ] `testInvalidCredentialsThrowAuthenticationException` — comportamento esperado
- [ ] `testAuthClassNotExtendingAbstractAuthenticableThrowsException` — comportamento esperado (validação de `classExtends`)
- [ ] `testEndpointWithoutAnyAuthClassConfiguredAllowsAccess` `[_bug]` — `ignoreAuth = false` mas nenhuma `authClass`/`authMethod` resolvida (nem endpoint, nem módulo, nem global) ainda assim libera acesso (fail-open)

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
