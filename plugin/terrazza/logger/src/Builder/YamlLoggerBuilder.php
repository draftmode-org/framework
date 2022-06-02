<?php
namespace Terrazza\Logger\Builder;

use RuntimeException;
use Terrazza\Logger\Factory\Writer\FlatStdOut;

class YamlLoggerBuilder {
    private string $namespace;
    private array $content                          = [];
    const ROOT_NODE                                 = "Logger";
    const CHANNEL_NODE                              = "Channel";
    const HANDLER_NODE                              = "Handler";
    const TYPE_NODE                                 = "Type";
    const REF_NODE                                  = "\$ref";
    const NAMESPACE_DELIMITER                       = ".";
    CONST PROPERTIES_NODE                           = "Properties";
    public function __construct(string $namespace=null) {
        $this->namespace = $namespace ??
            join("\\", array_slice(explode("\\", __NAMESPACE__), 0, -1));
    }

    function getLogger(string $yamlFileName) : void {
        $this->loadFile($yamlFileName);
        $channels                                   = $this->getChannels();
        $channels                                   = $this->setLogHandler($channels);
        var_dump($channels);
    }

    private function loadFile(string $yamlFileName) : void {
        if (file_exists($yamlFileName)) {
            $content                                = @yaml_parse_file($yamlFileName);
            if (is_array($content)) {
                if (array_key_exists(self::ROOT_NODE, $content)) {
                    $this->content                      = $content;
                } else {
                    throw new RuntimeException("node ".self::ROOT_NODE." missing in yaml.file $yamlFileName");
                }
            } else {
                throw new RuntimeException("yaml.file $yamlFileName could no be parsed");
            }
        } else {
            throw new RuntimeException("yaml.file $yamlFileName does not exist");
        }
    }

    private function setLogHandler(array $channels) : array {
        $result                                     = [];
        if (array_key_exists(self::HANDLER_NODE, $this->content[self::ROOT_NODE])) {
            $handlers                               = $this->content[self::ROOT_NODE][self::HANDLER_NODE];
            foreach ($handlers as $iHandler => $handler) {
                $exceptionNode                      = self::ROOT_NODE."/".self::HANDLER_NODE."/".$iHandler;

                // handle Level
                $nodeName                           = "Level";
                if (!array_key_exists($nodeName, $handler)) {
                    throw new RuntimeException("node $exceptionNode/$nodeName does not exist");
                }
                $level                              = $handler[$nodeName];

                // handle channels
                $nodeName                           = "Channel";
                if (!array_key_exists($nodeName, $handler)) {
                    throw new RuntimeException("node $exceptionNode/$nodeName does not exist");
                }
                $logChannels                        = $handler[$nodeName];
                if (!is_array($logChannels)) {
                    throw new RuntimeException("node $exceptionNode/$nodeName expected array, given ".gettype($logChannels));
                }
                foreach ($logChannels as $logChannel) {

                }
            }
            return $result;
        } else {
            throw new RuntimeException("node ".self::ROOT_NODE."/".self::HANDLER_NODE." does not exist");
        }
    }

    private function getHandler() : void {

    }

    private function getChannels() : array {
        $result                                     = [];
        if (array_key_exists(self::CHANNEL_NODE, $this->content[self::ROOT_NODE])) {
            $channels                               = $this->content[self::ROOT_NODE][self::CHANNEL_NODE];
            foreach ($channels as $channelName => $channel) {
                if (!is_array($channel)) {
                    throw new RuntimeException("node ".self::ROOT_NODE."/".self::CHANNEL_NODE."/$channelName content has to be an array, given ".gettype($channel));
                }
                $typeName                           = self::ROOT_NODE."/".self::CHANNEL_NODE."/$channelName";
                $typeClassName                      = $this->getTypeClassName($typeName, $channel);
                if (array_key_exists(self::PROPERTIES_NODE, $channel)) {
                    $result[$channelName]           = $this->loadClass($typeClassName, $channel[self::PROPERTIES_NODE]);
                } else {
                    $result[$channelName]           = $this->loadClass($typeClassName);
                }
            }
            return $result;
        } else {
            throw new RuntimeException("node ".self::ROOT_NODE."/".self::CHANNEL_NODE." does not exist");
        }
    }

    /**
     * @param class-string<T> $className
     * @param array|null $properties
     * @template T of object
     * @return T
     */
    private function loadClass(string $className, array $properties=null) {
        if (is_array($properties)) {
            return new $className(...array_values($properties));
        } else {
            return new $className;
        }
    }

    private function getTypeClassName(string $typeName, array $channel) : string {
        while (array_key_exists(self::REF_NODE, $channel)) {
            $propertyRef                            = $channel[self::REF_NODE];
            $channel                                = $this->getContentByRef($propertyRef);
        }
        if (array_key_exists(self::TYPE_NODE, $channel)) {
            $typeClassName                          = $channel[self::TYPE_NODE];
        } else {
            throw new RuntimeException("node $typeName/Type does not exist");
        }
        $typeClassName                              = $this->namespace."\\".str_replace(self::NAMESPACE_DELIMITER, "\\", $typeClassName);
        if (class_exists($typeClassName)) {
            return $typeClassName;
        } else {
            throw new RuntimeException("class $typeClassName in node $typeName/Type does not exist");
        }
    }

    private function getContentByRef(string $ref) : array {
        $content                                = $this->content[self::ROOT_NODE];
        $refs                                   = explode("/", $ref);
        array_shift($refs);
        $nodes                                  = [];
        foreach ($refs as $refKey) {
            $nodes[]                            = $refKey;
            if (array_key_exists($refKey, $content)) {
                $content                        = $content[$refKey];
            } else {
                throw new RuntimeException("ref ".join("/", $nodes). " does not exist");
            }
        }
        if (count($nodes) === 0) {
            throw new RuntimeException("ref $ref does not exist");
        }
        if (!is_array($content)) {
            throw new RuntimeException("ref ".join("/", $nodes). " exists, but content has to be an array");
        }
        return $content;
    }
}