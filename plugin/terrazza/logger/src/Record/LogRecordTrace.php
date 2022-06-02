<?php
namespace Terrazza\Logger\Record;

class LogRecordTrace {
    private string $namespace;
    private string $classname;
    private string $function;
    private ?int $line;

    /**
     * @param string $namespace
     * @param string $classname
     * @param string $function
     * @param int|null $line
     */
    public function __construct(string $namespace, string $classname, string $function, int $line=null)
    {
        $this->namespace = $namespace;
        $this->classname = $classname;
        $this->function = $function;
        $this->line = $line;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getClassname(): string
    {
        return $this->classname;
    }

    /**
     * @return string
     */
    public function getFunction(): string
    {
        return $this->function;
    }

    /**
     * @return int|null
     */
    public function getLine(): ?int
    {
        return $this->line;
    }


}