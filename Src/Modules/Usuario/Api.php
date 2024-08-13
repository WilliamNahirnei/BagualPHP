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
 * TESTES DEPLOY AUTOMATICO DOCUMENTACAO arq 2
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