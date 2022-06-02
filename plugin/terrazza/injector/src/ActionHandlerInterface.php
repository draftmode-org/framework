<?php
namespace Terrazza\Injector;

interface ActionHandlerInterface {
    public function execute(ActionInterface $action);
}