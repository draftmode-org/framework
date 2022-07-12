<?php
namespace Terrazza\Annotation\ReflectionClass;

use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Terrazza\Annotation\ReflectionClassNameResolverInterface;

class ClassNameResolver implements ReflectionClassNameResolverInterface {
    private ?string $classSourceFile = null;
    private ?array $classUseStatements = null;

    /**
     * @param string $parentClass
     * @param class-string<T> $findClass
     * @return T|null
     * @template T of object
     * @throws RuntimeException
     */
    public function getClassName(string $parentClass, string $findClass): ?string {
        if (class_exists($findClass)) {
            return $findClass;
        }
        try {
            $rClass = new ReflectionClass($parentClass);
        } catch (ReflectionException $exception) {
            throw new RuntimeException("parentClass $parentClass could not be loaded", $exception->getCode(), $exception);
        }
        $classInOwnNamespace = $rClass->getNamespaceName() . "\\" . $findClass;
        if (class_exists($classInOwnNamespace)) {
            return $classInOwnNamespace;
        }
        $useStatements                              = $this->getParseUseStatements($rClass);
        $searchKey                                  = array_search($findClass, array_column($useStatements, 'as'));
        if ($searchKey !== false) {
            return $useStatements[$searchKey]['class'];
        }
        $backSlashPos = strpos($findClass, "\\");
        if ($backSlashPos !== false) {
            $arrClass                               = explode("\\", $findClass);
            $classPrefix                            = array_shift($arrClass);
            $classPost                              = implode("\\", $arrClass);
            $asKey                                  = array_search($classPrefix, array_column($useStatements, 'as'));
            if ($asKey !== false) {
                $fullClass                          = $useStatements[$asKey]['class'] . "\\" . $classPost;
                if (class_exists($fullClass)) {
                    return $fullClass;
                };
            }
        }
        return null;
    }

    /**
     * @param ReflectionClass $rClass
     * @return array
     */
    private function getParseUseStatements(ReflectionClass $rClass): array {
        /*
         * reload useStatements
         * if never loaded
         * or already loaded reflection class filename is different
         */
        if (!$this->classSourceFile || $this->classSourceFile !== $rClass->getFileName()) {
            $this->classUseStatements               = null;
        }
        if (!$this->classUseStatements) {
            $this->classUseStatements               = $this->getClassTokenizer($rClass);
        }
        return $this->classUseStatements;
    }

    /**
     * @param ReflectionClass $rClass
     * @return string
     */
    private function getClassSource(ReflectionClass $rClass): string {
        $file = fopen($rClass->getFileName(), 'r');
        $line = 0;
        $source = '';
        while (!feof($file)) {
            ++$line;
            if ($line >= $rClass->getStartLine()) {
                break;
            }
            $source .= fgets($file);
        }
        fclose($file);
        return $source;
    }

    /**
     * @param ReflectionClass $rClass
     * @return array
     */
    private function getClassTokenizer(ReflectionClass $rClass): array {
        $source                                     = $this->getClassSource($rClass);
        $tokens                                     = token_get_all($source);
        $builtNamespace                             = '';
        $buildingNamespace                          = false;
        $matchedNamespace                           = false;

        $useStatements                              = [];
        $record                                     = false;
        $currentUse = [
            'class'                                 => '',
            'as'                                    => ''
        ];

        foreach ($tokens as $token) {
            if ($token[0] === T_NAMESPACE) {
                $buildingNamespace                  = true;
                if ($matchedNamespace) {
                    break;
                }
            }
            if ($buildingNamespace) {
                if ($token === ';') {
                    $buildingNamespace              = false;
                    continue;
                }
                switch ($token[0]) {
                    case T_STRING:
                    case T_NS_SEPARATOR:
                    case T_NAME_QUALIFIED:
                        $builtNamespace             .= $token[1];
                        break;
                }
                continue;
            }
            if ($token === ';' || !is_array($token)) {
                if ($record) {
                    $useStatements[]                = $currentUse;
                    $record                         = false;
                    $currentUse                     = [
                        'class'                     => '',
                        'as'                        => ''
                    ];
                }
                continue;
            }
            // @codeCoverageIgnoreStart
            if ($token[0] === T_CLASS) {
                break;
            }
            // @codeCoverageIgnoreEnd
            if (strcasecmp($builtNamespace, $rClass->getNamespaceName()) === 0) {
                $matchedNamespace                   = true;
            }
            if ($matchedNamespace) {
                if ($token[0] === T_USE) {
                    $record                         = 'class';
                }
                if ($token[0] === T_AS) {
                    $record                         = 'as';
                }
                if ($record) {
                    switch ($token[0]) {
                        case T_STRING:
                        case T_NS_SEPARATOR:
                        case T_NAME_QUALIFIED:
                            $currentUse[$record]    .= $token[1];
                            break;
                    }
                }
            }
            // @codeCoverageIgnoreStart
            // (2021-10-04) actually no testCase found/created
            if ($token[2] >= $rClass->getStartLine()) {
                break;
            }
            // @codeCoverageIgnoreEnd
        }

        foreach ($useStatements as &$useStatement) {
            if (empty($useStatement['as'])) {
                // (2021-10-04) actually no testCase found/created
                // @codeCoverageIgnoreStart
                $useStatement['as'] = basename(str_replace("\\", "/", $useStatement['class']));
                // @codeCoverageIgnoreEnd
            }
        }
        return $useStatements;
    }
}