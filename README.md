# BagualPHP

## Descrição

O Bagual PHP é um framework, pensado na construção de APIs utilizando PHP de maneira simples, padronizada, consiste e modular.

## Funcionalidades

- **Roteamento**: Define e gerencia rotas para diferentes métodos HTTP (GET, POST, PUT, DELETE).
- **Requisições**: Manipula dados de requisições HTTP, incluindo parâmetros de consulta, corpo da requisição e cabeçalhos.
- **Respostas**: Gera e envia respostas HTTP com códigos de status e mensagens apropriadas.
- **Autenticação**: Implementa uma estrutura básica de autenticação.
- **Erros**: Gerencia exceções e erros, fornecendo mensagens e códigos de status apropriados.
- **Configurações**: Gerencia configurações personalizadas de arquivos .env no sistema.

## Requisitos

- PHP 7.4 ou superior

## Instalação

1. Crie um novo projeto através do composer com o comando :
    ```bash
    composer create-project william-nahirnei/bagual-php nome-projeto
    ```

2. Navegue para o diretório do projeto:
    ```bash
    cd nome-projeto
    ```

3. Configure seu ambiente PHP para atender aos requisitos do projeto.

4. **Execute o Servidor PHP:**
    - O servidor PHP deve ser executado na raiz do arquivo index.php projeto para garantir que todas as rotas e configurações sejam corretamente carregadas:
    ```bash
    php -S localhost:8000
    ```

## Uso

### Estrutura do Projeto

A estrutura do projeto deve seguir o seguinte formato:
```

    src/
    │
    ├── Modules/
    │ ├── ModuloExemplo/
    │ │ ├── Api.php
    │ │ └── ... (outros arquivos do módulo)
    │ └── ... (outros módulos)
    └── ... (outros diretórios e arquivos)

```


Cada módulo deve ter um arquivo `Api.php`, que será lido pelo sistema para determinar os endpoints e configurar as rotas.

### Como Usar

1. **Roteamento**: O BagualPHP conta com um sistema de roteamento baseado em módulos.
   - A maneira correta de utilizar o BagualPHP é criar diretórios para cada um dos módulos da sua API dentro do diretório `Src/Modules`.
   - Após criar o diretório do seu módulo, você deve criar uma classe Api.php que deve estender ```Server\Routing\AbstractApi```. É nessa classe que seus endpoints serão definidos.
   - O BagualPHP faz uma busca por todos os diretórios de módulos dentro de `Src/Modules` e realiza a leitura automática dos endpoints definidos na sua classe `Api.php`.
   - Defina o atributo ```protected ?string $moduleName = "usuario";``` O atributo `moduleName` define um nome padrão para o módulo. Caso você não defina uma rota específica para os endpoints, o BagualPHP assumirá o nome do módulo como rota. Por exemplo, o endpoint será acessível em: `http://localhost:8080/usuario`.
   - O atributo `defaultAuthClass` e o atributo `defaultAuthMethod` devem ser definidos para especificar uma classe e um método de autenticação para o módulo. Quando um endpoint for acionado, o BagualPHP irá autenticar conforme a classe e o método definidos. Caso você não queira autenticar o módulo, defina esses dois atributos como nulos. Leia mais sobre autenticação na seção de 'Autenticação'.
   ```php
       protected ?string $defaultAuthClass = TokenAuth::class;
       protected ?string $defaultAuthMethod = "authenticate";
   ```
   - O atributo `ignoreAuth`, é usado para identificar se o modulo ira ignorar a autenticação definida seja nas configurações do sistema, modulo, ou endpoint.
   - Defina o construtor da sua classe Api, enviando os valores para o construtor da classe pai.
     ```php
        public function __construct() {
            parent::__construct(
                $this->moduleName,
                $this->defaultAuthClass,
                $this->defaultAuthMethod,
                $this->ignoreAuth
            );
        }
     ```
   - Definindo endpoints: A lista de endpoints deve ser definida dentro do método público `defineEndpointList`.
   - addEndpoint: Cada endpoint é definido pela chamada do método `addEndpoint(método [GET, POST, PUT, DELETE], "nomeEndpoint", Classe que irá responder à chamada, "nome do método que irá responder à chamada", classe de autenticação, "nome do método de autenticação", ignorar autenticação)`.
   ```php
        $this->addEndpoint(static::METHOD_GET, null, UsuarioController::class, "listar");
   ```
   - Os métodos de controlador e de autenticação deverão ser estáticos.
   - Abaixo, temos um exemplo de uma classe `Api.php` completa para um módulo de usuário.
    ```php
    <?php
    namespace Src\Modules\Usuario;

    use Server\Routing\AbstractApi;
    use Src\Modules\Usuario\Auth\TokenAuth;
    use Src\Modules\Usuario\Controller\UsuarioController;

    class Api extends AbstractApi {

        protected ?string $moduleName = "usuario";

        protected ?string $defaultAuthClass = null;

        protected ?string $defaultAuthMethod = null;

        protected ?bool $ignoreAuth = false;

        public function __construct() {
            parent::__construct(
                $this->moduleName,
                $this->defaultAuthClass,
                $this->defaultAuthMethod,
                $this->ignoreAuth
            );
        }

        public function defineEndpointList(): void {
            $this->addEndpoint(static::METHOD_GET, null, UsuarioController::class, "listar", TokenAuth::class, "authenticate");
            $this->addEndpoint(static::METHOD_POST, null, UsuarioController::class, "criar", TokenAuth::class, "authenticate");
            $this->addEndpoint(static::METHOD_PUT, null, UsuarioController::class, "atualizar", TokenAuth::class, "authenticate");
            $this->addEndpoint(static::METHOD_DELETE, "deletar", UsuarioController::class, "deletar", TokenAuth::class, "authenticate");
            $this->addEndpoint(static::METHOD_GET, "publico", UsuarioController::class, "publico");
        }
    }
    ?>

    ```
3. **Request:** A classe `Request` é a classe que disponibiliza acesso a alguns dados da requisição feita para a sua aplicação. A classe `Request` implementa a `InterfacePHPRequest`, responsável por definir as constantes para alguns índices das variáveis superglobais.
   - A classe Request é um singleton que é inicializado após o sistema determinar se o prefixo da API da requisição é válido em relação ao que foi configurado.
   - A classe Request disponibiliza alguns dados importantes, como parâmetros de query string, parâmetros do corpo da requisição, arquivos e headers. Para acessar esses dados, utilize a classe Request. Para acessar parâmetros da query string, como em site.com/user?idUser=1&qtdRegistros=15, você pode usar:
       ```php
           $parametrosQueryString = Request::getInstance()->getQueryParams();
       ```
   - Para acessar parâmetros do corpo da requisição, utilize:
       ```php
           $parametrosBody = Request::getInstance()->getBodyParams();
       ```
   - Para acessar parâmetros de query string e do corpo da requisição, utilize:
       ```php
           $todosParametros = Request::getInstance()->getAllMergedParams();
       ```
   - Esse método retorna os parâmetros da query string e do corpo da requisição, substituindo os parâmetros da query string pelos parâmetros do corpo da requisição quando os nomes forem iguais.
   - Para acessar os headers, utilize:
       ```php
           $headers = Request::getInstance()->getHeaders();
       ```
   - Para acessar outros dados da requisição, consulte a documentação da classe para ver os dados disponibilizados e os métodos para recuperá-los.

4. **Response**: A classe Response é responsável por gerenciar e montar a resposta para a sua requisição.
   - Para definir os dados de resposta da requisição, basta retornar os dados desejados no método que foi acionado pelo endpoint. Automaticamente, a classe Response tentará converter os dados informados para JSON.
   - A classe Response implementa algumas interfaces para valores padrão de resposta e a interface `InterfaceHeaders`, que define as strings padrão para headers de resposta. 
   - O valor padrão de uma resposta, caso o método do controlador não retorne nada, é o seguinte conteúdo, com código HTTP padrão 200:
   ```json
   {
       "message": "",
       "data": null
   }
   ```
   - Para definir um código HTTP de status, utilize:
   ```php
       Response::setStatusCode(StatusCodes::HTTP_OK);
   ```
   - Os códigos de resposta HTTP estão definidos em constantes da classe StatusCodes.
   - Para adicionar um header de resposta, utilize `addHeader(nomeHeader, valorHeader)`:
   ```php
           Response::addHeader(Response::HEADER_CONTENT_TYPE, Response::CONTENT_TYPE_JSON);
   ```
   - Para adicionar mais de um valor ao mesmo header, utilize o método `addHeader`, informando o nome do header e o valor adicional.

5. **Autenticação**: O BagualPHP oferece uma maneira de definir métodos de autenticação de forma customizada e simples para sua API. Basta desenvolver a lógica de autenticação e definir quais serão os locais autenticados no seu sistema.
   - Autenticação geral de api.
       - Para autenticar toda a sua API em um único local, crie uma classe de autenticação que estenda a classe `AbstractAuthenticable` com o método estático `authenticate`. Esse método deverá retornar `true` ou `false`, indicando se a autenticação foi bem-sucedida. Além disso, implemente o método `callAuthError`, que deverá lançar uma exceção caso a autenticação não seja válida.
       - Aconselhamos sempre utilizar a ```AuthenticationException``` para autenticações inválidas.
       - Após a implementação da sua autenticação, configure o arquivo `envsConfigs/.auth.env` com o nome da classe de autenticação criada. Depois disso, o sistema irá automaticamente autenticar toda a sua API. Caso a sua classe de autenticação apenas retorne `true` ou `false`, o sistema utilizará automaticamente um erro padrão de autenticação.
       - Todas as classes de autenticação deverão estender a classe ```AbstractAuthenticable```.
       - O método principal de autenticação a ser chamado pelo BagualPHP deverá sempre ser estático e não deve ter parâmetros.
       - Aqui está um exemplo de uma classe de autenticação padrão para o sistema:
         ```php
             <?php
    
            namespace Src\Auth;
            
            use Server\Auth\AbstractAuthenticable;
            use Server\Constants\ServerMessage;
            use Server\Errors\AuthenticationException;
            
            class GeneralAuth extends AbstractAuthenticable{
                /**
                 * This method must be implemented by subclasses and must throw AuthenticationException
                 *
                 * @throws AuthenticationException
                 */
                protected static function callAuthError(): void {
                    throw new AuthenticationException([ServerMessage::DEFAULT_AUTH_ERROR]);
                }
            
                public static function authenticate() {
                    return false;
                }
            }
            ?>
         ```
       - Aqui está o arquivo de configuração `.env` configurado:
         ```env
             DEFAULT_CLASS_NAMESPACE = Src\Auth\GeneralAuth
         ```
   - Autenticação de Módulo:
       - Como mencionado anteriormente, você pode definir autenticações de maneira isolada para módulos e métodos. Para definir uma autenticação isolada para todo um módulo, configure as variáveis a seguir no arquivo `Api.php` do seu módulo. Caso não queira autenticar o módulo, defina essas variáveis como `null`.
         ```php
            protected ?string $defaultAuthClass = TokenAuth::class;

            protected ?string $defaultAuthMethod = "authenticate";
         ```
   - Autenticação de Endpoint:
       - Caso queira autenticar somente um endpoint do módulo, defina a autenticação no método ```addEndpoint``` para o endpoint que deseja autenticar. Alternativamente, você pode autenticar todo o módulo e usar a variável de ignorar autenticação para todos os endpoints, exceto aquele que deseja autenticar.
         ```php
            $this->addEndpoint(static::METHOD_GET, null, UsuarioController::class, "listar", TokenAuth::class, "authenticate");
         ```
   - Ignorando Autenticações:
     - Você também pode ignorar autenticações em sua API, seja em nível de módulo ou de endpoint. Para ignorar uma autenticação definida globalmente para a API, em nível de módulo, defina a variável ```$ignoreAuth = true``` no arquivo `Api.php` do módulo.
     - Para ignorar a autenticação de um endpoint específico, seja global ou de módulo, envie o parâmetro `ignoreAuth` como `true` no método `addEndpoint`.
   - Prioridades de Autenticação:
     - O BagualPHP sempre tentará aplicar a autenticação seguindo uma ordem de prioridade específica. Caso não tenha sido definida autenticação para um nível específico, o BagualPHP tentará assumir a autenticação do nível superior. A ordem de prioridade é a seguinte: do item mais específico para o item mais genérico.
           - 1: Método ```addEndpoint```, autenticação específica do endpoint.
           - 2: Autenticação padrão do módulo.
           - 3: Autenticação geral da API.
     - Caso você envie valores nulos para autenticação no método ```addEndpoint```, o BagualPHP tentará usar os valores definidos no módulo e, se necessário, recorrerá à autenticação geral da API.
5. **Erros**: O BagualPHP conta com um sistema de tratamento personalizado para exceções do tipo ```ApiException```. Quando o BagualPHP encontrar uma exceção desse tipo, ele retornará uma resposta de API com o código HTTP definido na exceção e a mensagem definida na exceção. Aqui está um exemplo de como lançar uma exceção desse tipo:
   ```php
    throw new ApiException(true, ApiExceptionTypes::ERROR, ["Usuario não encontrado"], StatusCodes::HTTP_NOT_FOUND);
   ```
   - Caso a sua exceção tenha mais de uma mensagem, o BagualPHP retornará a lista de mensagens concatenadas, separadas pelo caractere `|`.
6. **Configurações personalizadas**: O BagualPHP conta com um gerenciamento de configurações para arquivos `.env` personalizados, que você pode utilizar para criar e administrar configurações para suas APIs.
   - Para utilizar as configurações personalizadas, crie um arquivo de configuração .env dentro da pasta `envsConfigs`.
   - Como, por exemplo, o arquivo de configuração `exemplo.env`.
   ```env
       CFG_EXEMPLO = TESTE
   ```
   - Crie uma classe que estenda ```Config/ConfigLoader.php```. Dentro da classe, defina uma constante com o nome do seu arquivo de configuração.
     ```php
       protected const FILE_NAME = '.exemplo.env';
     ```
     Defina o array de configurações de leitura permitida
     ```php
         protected const CONFIG_KEYS = ["CFG_EXEMPLO"];
     ```
     Pronto, suas configurações estão preparadas para serem utilizadas. Você pode criar múltiplos arquivos de configuração conforme necessário para diferentes usos.
   - Aqui está o exemplo da classe de teste completa:
     ```php
        <?php
        
        namespace Src\Modules\Usuario;
        
        use Config\ConfigLoader;
        
        class ConfigExemplo extends ConfigLoader {

            protected const FILE_NAME = '.exemplo.env';
        
            public const CFG_EXEMPLO = "CFG_EXEMPLO";
        
            protected const CONFIG_KEYS = [
                self::CFG_EXEMPLO,
            ];
        }
        ?>
     ```
     - Para realizar a leitura das suas configurações, basta utilizar:
     ```php
         ConfigExemplo::getInstance()->getConfig("Nome da configuração definida no arquivo .env");
     ```


Licença

Este projeto é licenciado sob a Licença MIT.
Contato

Se você tiver alguma dúvida ou sugestão, sinta-se à vontade para entrar em contato:

    E-mail: william.nahirnei@gmail.com
    GitHub: WilliamNahirnei
