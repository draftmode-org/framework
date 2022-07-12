<?php
require "../plugin/autoload.php";
use Terrazza\Kernel\HttpKernel;
putenv("APP_ENV=dev");
putenv("APP_DEBUG=false");
putenv("APP_NAME=Framework");
putenv("APP_CONFIG_FOLDER=../config");



use Terrazza\Annotation\ReflectionClass\ClassNameResolver;
use Terrazza\Annotation\ReflectionTypes\ReflectionTypesBuilder;

$classNameResolver = new ClassNameResolver();
echo "<h2>myVar</h2>";
$s = new Terrazza\Deserializer\Denormalizer(new ReflectionTypesBuilder($classNameResolver));
$s->denormalizeClass(\Terrazza\Framework\Application\Model\Annotation\MyAnnotationClass::class);

echo "<h2>myInline</h2>";
$s = new Terrazza\Deserializer\Denormalizer(new ReflectionTypesBuilder($classNameResolver));
$s->denormalizeClass(\Terrazza\Framework\Application\Model\Inline\MyInlineClass::class);
die();
//(new HttpKernel(getenv("APP_ENV"), getenv("APP_DEBUG")==="true"))->
//    handle(getenv("APP_FRAMEWORK"),getenv("APP_CONFIG_FOLDER"));
