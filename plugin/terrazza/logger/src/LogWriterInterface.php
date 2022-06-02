<?php

namespace Terrazza\Logger;

interface LogWriterInterface {
    public function write(array $record) : void;
}