# Projeto de API Server

## Descrição

Este projeto é uma biblioteca PHP projetada para facilitar o roteamento e o gerenciamento de APIs. A biblioteca é estruturada para funcionar com módulos, cada um definido em um diretório separado.

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

1. Clone o repositório:
    ```bash
    git clone https://github.com/WilliamNahirnei/PHP-APACHE-ROUTER.git
    ```

2. Navegue para o diretório do projeto:
    ```bash
    cd seu-repositorio
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

1. **Defina seus módulos:** Crie um diretório para cada módulo em `src/Modules/`. Cada módulo deve ter um arquivo `Api.php` que define os endpoints para esse módulo.

2. **Configure os Endpoints:** No arquivo `Api.php`, defina os endpoints e métodos que serão usados pelo seu projeto. A framework irá ler esses arquivos para configurar as rotas de forma automática.

    - **Api.php**: O arquivo Api.php devera extender de Server\Routing\AbstractApi. Defina os atributos e o construtor.
    - **moduleName**: O atributo modulename, define um nome default no modulo, caso não defina uma rota para os endpoints, o framework ira assumir o nome do modulo como rota "http://localhost:8080/usuario".
    - **defaultAuthClass**: O atributo defaultAuthClass e defaultAuthMethod devem ser definidos para especificar uma  classe e metodo de autenticação para o modulo, ao endpoint ser acionado o framework ira autenticar conforme a classe e metodo definidos.
    O atributo ignoreAuth, é usado para identificar se o modulo ira ignorar a autenticação definida seja nas configurações do sistema, modulo, ou endpoint.
    - **definindo endpoints**: Os endpoints são definidos no metodo defineEndpointList.
    - **addEndpoint**: Cada enpoints é definido pela chamada do metodo addEndpoint(metodo[get, post, put, delete], "nomeEndpoint", Classe que ira responder a chamada, "nome do metodo que ira responder a chamada", classe de autenticação, "nome metodo autenticação", ignorar autenticação).
    - **metodos de controlador e autenticação**: Os metodos de controller, e autenticação deverão ser staticos.


    ```php
    <?php
    namespace Src\Modules\Usuario;

    use Server\Routing\AbstractApi;
    use Src\Modules\Usuario\Auth\TokenAuth;
    use Src\Modules\Usuario\Controller\UsuarioController;

    /**
     * Class Api
     * 
     * This class defines the API endpoints for the "usuario" module.
     * It extends the AbstractApi class and configures endpoints with their respective HTTP methods, controllers, and authentication.
     *
     * @package Src\Modules\Usuario
     * @author William Nahirnei Lopes
     */
    class Api extends AbstractApi {
        /**
         * @var string|null The name of the module. Defaults to "usuario".
         */
        protected ?string $moduleName = "usuario";

        /**
         * @var string|null The default authentication class to use. Defaults to null.
         */
        protected ?string $defaultAuthClass = null;

        /**
         * @var string|null The default authentication method to use. Defaults to null.
         */
        protected ?string $defaultAuthMethod = null;

        /**
         * @var bool The default value if ignore auth method.
         */
        protected ?bool $ignoreAuth = false;

        /**
         * Api constructor.
         * Initializes the parent class with the module name, authentication class, and authentication method.
         */
        public function __construct() {
            parent::__construct(
                $this->moduleName,
                $this->defaultAuthClass,
                $this->defaultAuthMethod,
                $this->ignoreAuth
            );
        }

        /**
         * Defines the list of API endpoints for the "usuario" module.
         * Configures the HTTP methods, endpoint paths, controllers, and authentication methods.
         *
         * @return void
         */
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
3. **Request:** A classe Request é a classe que disponibiliza acesso a alguns dados da requisição feita para a sua aplicação. A classe Request implementa a InterfacePHPRequest, responsavel por definir as constantes para alguns indices de variaveis superglobais.
   - A classe Request é um singleton inicializado apos o sistema determinar se o prefixo de api da request é valido com o que foi configurado.
   - A classe request disponibiliza alguns dados improtantes como parametros de query string, parametros de corpo da requisição, arquivos, e headers. Para acessar esses dados utilize a classe Request
   Para acessar parametros da queryString ex site.com/user?idUser=1&qtdRegistros=15 utilize:
       ```php
           $parametrosQueryString = Request::getInstance()->getQueryParams();
       ```
   - Para acessar parametros do corpo da requisição utilize:
       ```php
           $parametrosBody = Request::getInstance()->getBodyParams();
       ```
   - Para acessar parametros de query string, e compo de requisição utilize:
       ```php
           $todosParametros = Request::getInstance()->getAllMergedParams();
       ```
   - Esse metodo retorna os parametros da string e corpo da requisição substituindo parametros da query string por parametros de corpo de requisição quando os nomes forem iguais.
   - Para acessar os headers utilize:
       ```php
           $headers = Request::getInstance()->getHeaders();
       ```
   - Para acessar outros dados da requisição, consulte a documentação da classe, os dados disponibilizados e os metodos para recuperalos.

4. **Response**: A classe Response é responsavel por gerenciar, e montar uma resposta da sua requisição.
   - Para definir os dados de resposta da requisição, basta retornar os dados desejados no seu metodo que foi acionado pelo endpoint. Automaticamente a classe response tentara converter os dados informados para um json.
   - A classe response implementa algumas interfaces de valores padrão para a resposta, e a InterfaceHeaders, que define as strings padrões de  headers de resposta 
   - O valor padrão de uma response que o metodo do controlador não retorne nada o seguinte conteudo, com codigo http padrão 200:
   ```json
   {
       "message": "",
       "data": null
   }
   ```
   - Para definir um codigo http de status utilize:
   ```php
       Response::setStatusCode(StatusCodes::HTTP_OK);
   ```
   - Os codigos de resposta http estão definidos em constantes da classe StatusCodes.
   - Para adicionar um header de resposta utilize addHeader(nomeHeader, valorHeader):
   ```php
           Response::addHeader(Response::HEADER_CONTENT_TYPE, Response::CONTENT_TYPE_JSON);
   ```
   - Para adicionar mais de um valor ao mesmo header utilize o metodo addHeader informando o header a qual sera adicionado, e o valor.

5. **Autenticação**: O framework nomeframework, oferece uma maneira de definir metodos de autenticação de maneira customizada e simples para sua api. Basta desenvolver a maneira que ira autenticar, e definir quais sera os locais autenticados do seu sistema.
   - Autenticação geral de api.
       - Para autenticar o toda a sua api em um unico local, crie sua classe de autenticação que extenda a classe AbstractAuthenticable com o metodo estatico autenticate ```authenticate ``` esse metodo devera retornar um true ou false, e implemente o metodo ```callAuthError``` esse metodo devera chamar uma excessao caso a autenticação não seja valida.
       - Aconselhamos sempre utilizar a ```AuthenticationException``` para autenticações invalidas.
       - Apos a implementação da sua autenticação, configure o arquivo envsConfigs/.auth.env com o nome da classe de autenticação criada. Apos isso o sistema ira automaticamente autenticar toda sua api. Caso sua classe de autenticação somente retornar um true ou false, o sistema automaticamente ira utilizar um erro padrão de autenticação.
       - Todas as classes de autenticação deverão extender da classe ```AbstractAuthenticable```.
       - O metodo principal de autenticação a ser chamado pelo framework, devera sempre ser estatico, e não ter parametros.
       - aqui esta um exemplo de uma classe de autenticação padrão para sistema:
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
       - Aqui esta o arquivo de configuração .env configurado:
         ```env
             DEFAULT_CLASS_NAMESPACE = Src\Auth\GeneralAuth
         ```
   - Autenticação de modulo:
       - Como mencionado anteriormente você pode definir autenticações de manerias isolada para modulos, e metodos, para definir uma autenticação isolada, de todo um modulo, configure as variaveis a seguir do arquivo Api.php do seu modulo. Caso não queira autenticar o modulo defina-as como null.
         ```php
            /**
             * @var string|null The default authentication class to use. Defaults to null.
             */
            protected ?string $defaultAuthClass = TokenAuth::class;
        
            /**
             * @var string|null The default authentication method to use. Defaults to null.
             */
            protected ?string $defaultAuthMethod = "authenticate";
         ```
   - Autenticação de endpoint:
       - Caso queira  autenticar somente um enpoint do modulo defina a autenticação no metodo ```php addEnpoint ``` no endpoint que deseja autenticar, ou autentique todo o modulo, e envie a variavelde ignorar autenticação para todas os endpoints, exceto a que deseja autenticar.
         ```php
            $this->addEndpoint(static::METHOD_GET, null, UsuarioController::class, "listar", TokenAuth::class, "authenticate");
         ```
   - Ignorando autenticações:
     - Você tambem pode ignorar autenticações em sua api, seja em nivel de modulo ou de enpoint, para ignorar uma autenticação definida globalmente para a api, em nivel de modulo difna a variavel ```$ignoreAuth = true``` em seu arquivo Api.php do modulo.
     - Para ignorar somente um endpoint especifco, seja autenticação global, ou de modulo envie o parametro ```ignoreAuth``` como ```true``` para o metodo ```addEndpoint```.
   - Prioridades de autenticação:
     - O sistema sempre ira tentar assumir a autenticação em uma ordem de prioridade especifica, caso não tenha sido definida autenticação para aquele nivel o framework ira tentar sempre assumir a autenticação do nivel superior sendo essa ordem: do item mais especifico, para o item mais generico.
           - 1: Metodo ```addEndpoint```, autenticação especifica do endpoint.
           - 2: Auteticação padrão do modulo.
           - 3: Autenticação geral da api.
     -Caso você envie valores nullos de autenticação para o metodo ```addEndpoint```, o framework ira tentar assumir os valores definidos no modulo, e assim por diante.
5. **Erros**: Framework conta com um sistema de tratamento personalizado para excessões do tipo ```ApiException``` quando o framework tiver uma excessão desse tipo, ele ira adicionar retornar uma resposta de api, com o código http definido no erro, e a mensagem definida no erro. Aqui esta um exemplo do lançamento de uma excessão desse tipo:
   ```php
    throw new ApiException(true, ApiExceptionTypes::ERROR, ["Usuario não encontrado"], StatusCodes::HTTP_NOT_FOUND);
   ```
   - Caso a sua excessão tenha mais de uma mensagem, o framework ira retornar a lista de mensagens concatenadas separadas pelo caractere "|".
6. **Configurações personalizadas**: O framework conta com um gerenciamento de configurações para arquivos .env personalizados, que você pode utilizar para criar configurações para suas api, e realizar a admnistração delas.
   - Para utilizar as configurações personalizadas, crie um arquivo de configuração .env, dentro da pasta envsConfigs.
   - como por exemplo o arquivo de configuração ```exemplo.env```
   ```env
       CFG_EXEMPLO = TESTE
   ```
   - Crie uma classe que extenda de ```Config/ConfigLoader.php```. Dentro da classe defina a constante com o nome do seu arquivo de configuração
     ```php
       protected const FILE_NAME = '.exemplo.env';
     ```
     Defina o array de configurações de leitura permitida
     ```php
         protected const CONFIG_KEYS = ["CFG_EXEMPLO"];
     ```
     Pronto suas configurações estão prontas para serem utilizadas. Você pode criar N arquivos de configurações para determinada necessidade de utilização.
   - aqui exta o exemplo da classe de teste completa:
     ```php
        <?php
        
        namespace Src\Modules\Usuario;
        
        use Config\ConfigLoader;
        
        /**
         * Class AuthConfig
         * 
         * This class reads an test configuration file and loads the configurations from it.
         * It extends the ConfigLoader class and implements the Singleton pattern.
         *
         * @package Server\Auth
         * @author William Nahirnei Lopes
         */
        class ConfigExemplo extends ConfigLoader {
            /**
             * @var string The path to the authentication configuration file.
             */
            protected const FILE_NAME = '.exemplo.env';
        
            /**
             * The default config test name.
             *
             * @var string
             */
            public const CFG_EXEMPLO = "CFG_EXEMPLO";
        
            /**
             * @var array The list of teste configuration keys to load.
             */
            protected const CONFIG_KEYS = [
                self::CFG_EXEMPLO,
            ];
        }
        ?>
     ```
     - para realizar a leitura de suas configurações basta utilizar
     ```php
         ConfigExemplo::getInstance()->getConfig("Nome da configuração definida no arquivo .env");
     ```


Licença

Este projeto é licenciado sob a Licença MIT.
Contato

Se você tiver alguma dúvida ou sugestão, sinta-se à vontade para entrar em contato:

    E-mail: william.nahirnei@gmail.com
    GitHub: WilliamNahirnei
