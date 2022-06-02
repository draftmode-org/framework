<?php
namespace Terrazza\Injector;

interface ActionHandlerBuilderInterface {
    public function withMapper(array $actionMapper) : ActionHandlerInterface;
}