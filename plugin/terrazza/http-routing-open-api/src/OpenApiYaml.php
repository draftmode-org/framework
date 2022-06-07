<?php
namespace Terrazza\Http\Routing\OpenApi;

use RuntimeException;
use Terrazza\Http\Routing\HttpRouteLoaderInterface;

class OpenApiYaml implements HttpRouteLoaderInterface {
    private string $yamlFilename;
    private ?array $content                         = null;
    CONST REF_NODE                                  = "\$ref";
    CONST REQUEST_BODY_NODE                         = "requestBody";
    CONST REQUEST_HANDLER_CLASS_NODE                = "operationId";
    CONST SKIP_METHODS								= ["parameters"];
    CONST MULTIPLE_TYPES                            = ["oneOf"];

    public function __construct(string $yamlFilename) {
        $this->yamlFilename                         = $yamlFilename;
    }

    /**
     * @return array
     */
    public function getPaths() : array {
        $content									= $this->getContent();
        $paths 										= $content["paths"] ?? [];
        return array_keys($paths);
    }

    /**
     * @param string $uri
     * @param string $method
     * @return array|null
     */
    public function getPath(string $uri, string $method) :?array {
        if ($this->isAllowedMethod($method)) {
            $content								= $this->getContent();
            $uri 									= ltrim($uri, "/");
            foreach (["paths", "/$uri", $method] as $path) {
                if (array_key_exists($path, $content)) {
                    $parent 						= $content;
                    $content 						= $content[$path];
                } else {
                    return null;
                }
            }
            // protect missing parameters node
            // merge parent values
            $content["parent"]						= [
                "parameters"						=> $parent["parameters"] ?? []
            ];
            // clean unused attributes
            unset($content["summary"]);
            unset($content["tags"]);

            // return found path
            return $content;
        } else {
            return [];
        }
    }

    /**
     * @param string $uri
     * @param string $method
     * @param string|null $contentType
     * @return string
     * @throw RuntimeException
     */
    public function getRequestHandlerClass(string $uri, string $method, ?string $contentType=null) : string {
        if ($path = $this->getPath($uri, $method)) {
            $requestHandlerClass                    = $path[self::REQUEST_HANDLER_CLASS_NODE] ?? null;
            if ($requestHandlerClass) {
                return $requestHandlerClass;
            }
            throw new RuntimeException("node ".self::REQUEST_HANDLER_CLASS_NODE." for paths/$uri:$method not found");
        } else {
            throw new RuntimeException("uri $uri, method $method not found");
        }
    }

    /**
     * @param string $uri
     * @param string $method
     * @return array
     */
    public function getPathParameters(string $uri, string $method) : array {
        $parameters                                 = [];
        if ($path = $this->getPath($uri, $method)) {
            $this->pushParameters($parameters, $path["parameters"] ?? []);
            $this->pushParameters($parameters, $path["parent"]["parameters"]);
        }
        return $parameters;
    }

    /**
     * @param string $uri
     * @param string $method
     * @return array|null
     */
    public function getContentTypes(string $uri, string $method) :?array {
        if ($path = $this->getPath($uri, $method)) {
            // requestBody node exists
            if (array_key_exists(self::REQUEST_BODY_NODE, $path)) {
                $requestBodies                      = $path[self::REQUEST_BODY_NODE];

                // fix \$ref contents
                while (array_key_exists(self::REF_NODE, $requestBodies)) {
                    $ref                            = $requestBodies[self::REF_NODE];
                    $requestBodies                  = $this->getContentByRef($ref);
                }

                // node content exists
                // ...if not, ignore malformed yaml: return null (no contentTypes)
                $types                              = $requestBodies["content"] ?? null;
                if ($types) {
                    return array_keys($types);
                }
            }
        }
        return null;
    }

    /**
     * @param array $parameters
     * @param array $pathParameters
     */
    private function pushParameters(array &$parameters, array $pathParameters) : void {
        foreach ($pathParameters as $parameter) {
            $type                                   = $parameter["in"];
            unset($parameter["in"]);
            $name                                   = $parameter["name"];
            unset($parameter["name"]);
            if (!array_key_exists($type, $parameters)) {
                $parameters[$type]                  = [];
            }
            $parameters[$type][$name]               = $this->getEncodedParameter($parameter);
        }
    }

    /**
     * @param array $parameter
     * @return array
     */
    private function getEncodedParameter(array $parameter) : array {
        // use schema Node
        $schemaNode                                 = "schema";
        $schema                                     = $parameter[$schemaNode] ?? [];
        if (array_key_exists("required", $parameter)) {
            $schema["required"]                     = $parameter["required"];
        }

        $schemaRequired                             = $schema["required"] ?? null;

        // encode \$ref
        while (array_key_exists(self::REF_NODE, $schema)) {
            $ref                                    = $schema[self::REF_NODE];
            $schema                                 = $this->getContentByRef($ref);
            if (array_key_exists("required", $schema)) {
                $schemaRequired                     = $schema["required"];
            }
        }

        // convert multiple types (e.g. oneOf)
        foreach (self::MULTIPLE_TYPES as $multipleType) {
            if (array_key_exists($multipleType, $schema)) {
                $schema["type"]                     = $multipleType;
                $schema["properties"]               = [];
                foreach ($schema[$multipleType] as $childProperty) {
                    $schema["properties"][]         = $childProperty;
                }
            }
        }

        //
        if (array_key_exists("type", $schema)) {
            if (array_key_exists("properties", $schema)) {
                $childSchemas                       = [];
                $schemaProperties                   = $schema["properties"] ?? [];
                foreach ($schemaProperties as $childName => $childProperty) {
                    $childSchema                    = $this->getEncodedParameter($childProperty ?? []);
                    if (is_array($schemaRequired) &&
                        in_array($childName, $schemaRequired)) {
                        $childSchema["required"]    = true;
                    }
                    $childSchemas[$childName]       = $childSchema;
                }
                $schema["properties"]               = $childSchemas;
                unset($schema["required"]);
            } else {
                if ($schemaRequired === true) {
                    $schema["required"]             = true;
                }
            }
            foreach (self::MULTIPLE_TYPES as $multipleType) {
                unset($schema[$multipleType]);
            }
            return $schema;
        } else {
            throw new RuntimeException("node type for does not exist");
        }
    }

    /**
     * @param string $ref
     * @return array
     */
    private function getContentByRef(string $ref) : array {
        $content                                = $this->getContent();
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

    /**
     * @return array
     */
    private function getContent(): array {
        if (is_null($this->content)) {
            if (file_exists($this->yamlFilename)) {
                $this->content = @yaml_parse_file($this->yamlFilename);
                if (!is_array($this->content)) {
                    throw new RuntimeException("yaml file does not provide any valid content");
                }
            } else {
                throw new RuntimeException("yaml file does not exists, given ".$this->yamlFilename);
            }
        }
        return $this->content;
    }

    /**
     * @param string $method
     * @return bool
     */
    private function isAllowedMethod(string $method) : bool {
        return !(in_array($method, self::SKIP_METHODS));
    }
}