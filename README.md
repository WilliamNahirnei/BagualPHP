# Projeto de API Server

## Descrição

Este projeto é uma biblioteca PHP projetada para facilitar o roteamento e o gerenciamento de APIs. A biblioteca é estruturada para funcionar com módulos, cada um definido em um diretório separado.

## Funcionalidades

- **Roteamento**: Define e gerencia rotas para diferentes métodos HTTP (GET, POST, PUT, DELETE).
- **Requisições**: Manipula dados de requisições HTTP, incluindo parâmetros de consulta, corpo da requisição e cabeçalhos.
- **Respostas**: Gera e envia respostas HTTP com códigos de status e mensagens apropriadas.
- **Autenticação**: Implementa uma estrutura básica de autenticação.
- **Erros**: Gerencia exceções e erros, fornecendo mensagens e códigos de status apropriados.

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

2. **Configure os Endpoints:** No arquivo `Api.php`, defina os endpoints e métodos que serão usados pelo seu projeto. A biblioteca irá ler esses arquivos para configurar as rotas de forma automática.

3. **Autenticação e Controle de Acesso:** Implementações de autenticação devem estender a classe `AbstractAuthenticable`. Verifique a documentação para detalhes sobre como configurar a autenticação.

4. **Inicialize o Roteador:** O roteador deve ser inicializado para começar a processar as requisições e gerenciar os endpoints configurados.

## Licença

Este projeto é licenciado sob a [Licença MIT](LICENSE).

## Contato

Se você tiver alguma dúvida ou sugestão, sinta-se à vontade para entrar em contato:

- **E-mail**: william.nahirnei@gmail.com
- **GitHub**: [WilliamNahirnei](https://github.com/WilliamNahirnei)