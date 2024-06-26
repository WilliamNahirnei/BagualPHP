<?php
namespace Server\Interfaces;

use Server\Constants\StatusCodes;

interface InterfaceDefaultValuesResponse
{
    public const DEFAULT_STATUS_CODE = StatusCodes::HTTP_OK;
    public const DEFAULT_CONTENT = [];
    public const DEFAULT_MESSAGE = "";
}
?>