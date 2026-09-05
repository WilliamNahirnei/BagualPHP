# CLAUDE.md

Este arquivo fornece orientações para o Claude Code (claude.ai/code) ao trabalhar com código neste repositório.

## O que é isso

BagualPHP é um micro-framework PHP (>=7.4) leve para construção de REST APIs modulares. Ele é distribuído como uma biblioteca/skeleton Composer (`composer create-project william-nahirnei/bagual-php`) — este repositório em si não possui código de aplicação em `Src/`, apenas o core do framework em `Autoloader/`, `Config/` e `Server/`. Consumidores do framework adicionam seus próprios arquivos `Src/Modules/<ModuleName>/Api.php`, que o framework descobre e conecta às rotas em tempo de requisição.

## Executando

Não há etapa de build. Sirva a raiz do projeto com o servidor embutido do PHP:

```bash
php -S localhost:8000
```

`index.php` requer o autoloader do Composer (`vendor/autoload.php`, mapeamento PSR-4 definido em `composer.json` para `Config\`, `Server\`, `Src\`) e despacha toda requisição através de `Server\Router\Router`. Não há scripts de teste, lint ou build definidos em `composer.json`.

## Ciclo de vida da requisição (leia isto antes de mexer em `Server/`)

1. `index.php` instancia `Router` e chama `executeRequest()`.
2. `Router` valida o prefixo de API configurado (`RouterConfig`, sustentado por `envsConfigs/.router.env`, chave `PREFIX_API`) contra a URI da requisição, remove-o e constrói o singleton `Request` (query params, corpo JSON, headers, arquivos).
3. `Request::sanitizeParams()` passa recursivamente toda string de query/corpo pela lista de bloqueio de regex em `Server\Interfaces\SQLInjectionPatterns` (palavras-chave DML/DDL/DCL/transação, `--`, `#`, `;`, `*`, `or`/`and`) antes que qualquer outra coisa toque na entrada do usuário. Qualquer mudança nos formatos de entrada aceitos deve passar por esse sanitizador.
4. `ApiManager::loadApiEndpoints()` percorre (glob) todo diretório em `Src/Modules/*` e, para cada um que tenha um `Api.php`, deriva o namespace a partir do caminho do diretório (`Src\Modules\<ModuleName>`) e instancia `Api`, chamando `defineEndpointList()`.
5. A classe `Api` de cada módulo estende `Server\Routing\AbstractApi` e chama `addEndpoint($method, $endpointPath, $controllerClass, $controllerMethod, $authClass, $authMethod, $ignoreAuth)` dentro de `defineEndpointList()`, que constrói um `Endpoint` e o registra na tabela de rotas estática (`Server\Routing\Route`, indexada por método HTTP e depois por URI).
6. `Router::processRequest()` procura o `Endpoint` correspondente e chama `executeEndpoint()`, que autentica (veja abaixo) e então invoca o método do controller via `call_user_func([$controllerClass, $controllerMethod])`. Antes de invocar, `validateExistenceEndpointExecutable()` confere apenas que a classe e o método do controller existem (via `Server\Suport\TraitSuportValidationClass`), lançando uma `\Exception` simples caso não existam. **Somente o método de auth precisa ser `static` e sem parâmetros** — essa checagem via reflection (`methodIsStatic`/`methodHasNoParameters`) roda apenas em `Endpoint::authClassIAutenticatle()`, para a classe/método de autenticação; o controller não passa por essa validação de assinatura.
7. O que o método do controller retornar se torna o corpo da resposta (codificado em JSON por `Server\Router\Response`); lance `Server\Errors\ApiException` (com `ApiExceptionTypes`, uma ou mais mensagens e um valor de `StatusCodes`) para erros esperados da API, ou deixe qualquer outro `\Throwable` cair para um 500 genérico.

## Ordem de resolução de autenticação

A autenticação é resolvida endpoint → módulo → global, com a mais específica prevalecendo, avaliada em `Endpoint::authenticate()`:
1. `authClass`/`authMethod` passados explicitamente para `addEndpoint()`.
2. As propriedades `defaultAuthClass`/`defaultAuthMethod` do módulo (definidas na classe `Api` do módulo).
3. O padrão global configurado via `envsConfigs/.auth.env` (`DEFAULT_CLASS_NAMESPACE`), carregado através de `Server\Auth\AuthConfig`.

`ignoreAuth` pode ser definido na chamada do endpoint ou no nível do módulo para pular isso completamente (o valor no nível do endpoint prevalece quando passado explicitamente). Qualquer classe de auth concreta deve estender `Server\Auth\AbstractAuthenticable`, implementar um `authenticate()` estático, sem argumentos, retornando bool, e implementar `callAuthError()` lançando `Server\Errors\AuthenticationException` em caso de falha.

## Padrão de carregamento de configuração

`Config\ConfigLoader` é uma base singleton abstrata: as subclasses (ex.: `RouterConfig`, `AuthConfig`) declaram `protected const FILE_NAME` (um arquivo dentro de `envsConfigs/`) e `protected const CONFIG_KEYS` (a lista de permissão de chaves lidas desse arquivo). Somente as chaves em `CONFIG_KEYS` são carregadas do arquivo no estilo `.env`. Novas configurações por funcionalidade devem seguir esse mesmo padrão (arquivo `.env` próprio em `envsConfigs/`, subclasse própria de `ConfigLoader`) em vez de adicionar chaves a um loader já existente.

## Convenções a preservar ao editar o core do framework

- Tudo em `Server/` e `Config/` é código de framework consumido por projetos externos — evite pressupor que existe um layout específico de `Src/Modules` neste repositório.
- As classes são consistentemente estruturadas como: propriedades privadas tipadas → construtor → getters/setters públicos/privados → métodos de comportamento, com blocos PHPDoc completos em todo método (`@param`, `@return`, `@throws`). Siga esse estilo em código novo.
- Os arquivos terminam com uma tag de fechamento `?>` (consistente em toda a base de código existente, incluindo `index.php`).
- Singletons usam um construtor privado + `getInstance()` estático; siga esse padrão em vez de introduzir DI para novos serviços do core.
- Erros de validação de rota/endpoint/auth usam a `\Exception` interna (via `TraitSuportValidationClass`) para erros de programação (classe/método ausente, assinatura incorreta), e `ApiException`/`AuthenticationException` para erros em tempo de requisição/voltados à API — mantenha essa distinção ao adicionar novas validações.
