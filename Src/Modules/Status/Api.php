<?php
    namespace Src\Modules\Status;
    use Server\Routing\AbstractApi;
    use Src\Modules\Status\Controller\StatusController;

    class Api extends AbstractApi {
        protected ?string $moduleName = "status";
        protected ?string $defaultAuthClass = null;
        protected ?string $defaultAuthMethod = null;
    
        public function __construct() {
            parent::__construct(
                $this->moduleName,
                $this->defaultAuthClass,
                $this->defaultAuthMethod
            );
        }
        public function defineEndpointList() {
            $this->addEndpoint(static::METHOD_GET, null, StatusController::class, "status");
            $this->addEndpoint(static::METHOD_POST, "testePost", StatusController::class, "testePost");

        }

    }


?>