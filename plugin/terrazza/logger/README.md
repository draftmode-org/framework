# Terrazza/Logger
This component is an implementation of PSR/Log standard with some extensions.

## _Structure_
1. the [Logger](#object-logger) component<br>
has to be initialized with 0-n [ChannelHandler](#object-channel-handler)<br>
and provides the common known methods:
   - warning
   - error
   - notice
   - ...
2. the [ChannelHandler](#object-channel-handler) component<br>
is responsible to determine 
   1. the target/writer (required)
   2. the recordFormatter (required)
   3. a channelFilter (optional)
   4. all related [LogHandler](#object-log-handler) for this channel
3. the [LogHandler](#object-log-handler) component<br>
determines
   - the logLevel
   - the format (optional, default: from ChannelHandler->recordFormatter)

_The Terrazza/Logger component differ to the common PSR/Log implementation in handling the "Format".
The Writer Component handles multiple "rows" and combines it. Within this difference its possible to forward a transformed format and keep his keys per row.<br>
For example: write the message/format into a json object or db._

## _Object/Classes_

1. [Logger](#object-logger)
   1. [method: registerChannelHandler](#object-logger-registerChannelHandler)
   2. [method: registerExceptionHandler](#object-logger-registerExceptionHandler)
   3. [method: registerErrorHandler](#object-logger-registerErrorHandler)
   4. [method: registerFatalHandler](#object-logger-registerFatalHandler)
   5. [method: setExceptionFileName](#object-logger-setExceptionFileName)
   6. [constructor: context (array)](#object-logger-constructor-context)
2. Handler
   1. [ChannelHandler](#object-channel-handler)
   2. [LogHandler](#object-log-handler)
3. [LogRecord](#object-record)
   1. [LogRecordTrace](#object-record-trace)
4. [LogRecordFormatter](#object-log-record-formatter)
5. [LogHandlerFilter](#object-log-handler-filter)
6. [Converter](#object-converter)
7. [Install](#install)
8. [Requirements](#require)
9. [Examples](#examples)

<a id="object-logger" name="object-logger"></a>
<a id="user-content-object-logger" name="user-content-object-logger"></a>
### Logger
The Logger object/methods is/are close to the common PSR implementations but!<br>
The Logger is initialized with channelHandler(s) and not Handler<br>
>each channelHandler executes only one logHandler

<a id="object-logger-registerChannelHandler" name="object-logger-registerChannelHandler"></a>
<a id="user-content-object-logger-registerChannelHandler" name="user-content-object-logger-registerChannelHandler"></a>
#### method: registerChannelHandler
adds a channelHandler to the logger (not immutable).<br>

<a id="object-logger-registerExceptionHandler" name="object-logger-registerExceptionHandler"></a>
<a id="user-content-object-logger-registerExceptionHandler" name="user-content-object-logger-registerExceptionHandler"></a>
#### method: registerExceptionHandler
register a callback for php exception handler.<br>
>well-developed projects should handle/cover all exceptions by themselves, but ;-)

<a id="object-logger-registerErrorHandler" name="object-logger-registerErrorHandler"></a>
<a id="user-content-object-logger-registerErrorHandler" name="user-content-object-logger-registerErrorHandler"></a>
#### method: registerErrorHandler
register a callback for php error handler.<br>
>sometime this kind of errors can't be catched without this workaround

<a id="object-logger-registerFatalHandler" name="object-logger-registerFatalHandler"></a>
<a id="user-content-object-logger-registerFatalHandler" name="user-content-object-logger-registerFatalHandler"></a>
#### method: registerFatalHandler
register a callback for php shutdown.<br>
>sometime this kind of errors can't be catched without this workaround

<a id="object-logger-setExceptionFileName" name="object-logger-setExceptionFileName"></a>
<a id="user-content-object-logger-setExceptionFileName" name="user-content-object-logger-setExceptionFileName"></a>
#### method: setExceptionFileName
The method:addMessage itself is covered with try/catch.<br>
The catch handler writes the Exception.Message to a file which can be set with the method setExceptionFileName.<br>
>notice:<br>
default: php://stderr

<a id="object-logger-constructor-context" name="object-logger-constructor-context"></a>
<a id="user-content-object-logger-constructor-context" name="user-content-object-logger-constructor-context"></a>
#### constructor: context (array)
The logger can be initialized, next to the name, with an initialized context.<br>
This context can be addressed separately.<br>

<i>example of usage</i><br>
a class is injected with the component and inside the constructor<br>
```
$logger = new Logger("name", ["user" => "Value"]);
$logger->notice("hello", ["my" => "value"]);
$format = ["{Context.my} {iContext.user}"];
```
### Handler
<a id="object-channel-handler" name="object-channel-handler"></a>
<a id="user-content-object-channel-handler" name="user-content-object-channel-handler"></a>
#### ChannelHandler
A ChannelHandler collects [LogHandler](#object-log-handler) to the same channel and provides
- the same writer
- the same formatter<br>
for each [LogHandler](#object-log-handler).

A ChannelHandler can be registered through the [Logger](#object-logger) with<br>
- the [method: registerChannelHandler](#object-logger-registerChannelHandler)
- the __constructor (3rd argument as variadic)

##### method: getWriter
##### method: getFormatter
##### method: getFilter
##### method: getLogHandler (LogHandlerInterface[])
##### method: pushLogHandler
Method to add a new [LogHandler](#object-log-handler).<br>
The logHandler-array will be key-sorted, to prevent multiple write transaction for different LogLevels.

##### method: getEffectedHandler
return the matched [LogHandler](#object-log-handler) for a given LogRecord.

##### method: writeRecord
for a passed [LogHandler](#object-log-handler) the record will be
- formatted
- and written to the Writer

<a id="object-log-handler" name="object-log-handler"></a>
<a id="user-content-object-log-handler" name="user-content-object-log-handler"></a>
#### LogHandler
The SingleHandler provides the common way to create a handler for a Logger.
The only difference to the common implementation:
- instead of logLevel
- the SingleHandler has to be injected within a [Channel](#object-channel)

<a id="object-record" name="object-record"></a>
<a id="user-content-object-record" name="user-content-object-record"></a>
### LogRecord
Against the common PSR implementation our component deals with an object and not an array.<br>
LogRecord properties:
- logDate (\Datetime)
- loggerName (string)
- logLevel (int) 
- logMessage (string)
- memUsed (int)
- memAllocated (int)
- [LogRecordTrace](#object-record-trace)
- context (array)
- initContext (array)

additional, the object provides
- logLevelName (string)

#### method/static createRecord
this method is used inside <i>Logger</i> to create a new LogRecord object.

#### method: getToken()
this method is used in the <i>LogRecordFormatterInterface</i> to get the LogRecord "encoded".
Every element can be accessed through his "format" e.g. {Level}{LevelName}{Context.name}<br>

```
return [
  'Date'         => $this->getLogDate(),
  'Level'        => $this->getLogLevel(),
  'LevelName'    => $this->getLogLevelName(),
  'LoggerName'   => $this->getLoggerName(),
  'MemUsed'      => $this->getMemUsed(),
  'MemAllocated' => $this->getMemAllocated(),
  'Message'      => $this->getLogMessage(),
  'Context'      => $this->getContext(),
  'iContext'     => $this->getInitContext(),
  'Trace'        => [
    "Namespace"    => $this->getTrace()->getNamespace(),
    "Line"         => $this->getTrace()->getLine(),
    "Classname"    => $this->getTrace()->getClassname(),
    "Function"     => $this->getTrace()->getFunction(),
    "Method"       => $this->getTrace()->getClassname()."::".$this->getTrace()->getFunction(),
    "sMethod"      => basename($this->getTrace()->getClassname())."::".$this->getTrace()->getFunction(),  
  ]
]
```

<a id="object-record-trace" name="object-record-trace"></a>
<a id="user-content-object-record-trace" name="user-content-object-record-trace"></a>
### LogRecordTrace
The LogRecordTrace object is generated in the [Logger](#object-logger) during a [LogRecord](#object-record) is created.<br>
Base on _debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)_ every Record get additional properties:
- Namespace
- Classname
- Function
- Line

<a id="object-log-record-formatter" name="object-log-record-formatter"></a>
<a id="user-content-object-log-record-formatter" name="user-content-object-log-record-formatter"></a>
### LogRecordFormatter
The LogRecordFormatter converts/maps a record to an array<br>.
Initialized properties:
- NonScalarConverterInterface
- format (array)
- valueConverter (array, optional)

#### NonScalarConverter (NonScalarConverterInterface)
The NonScalarConverter convert a nonScalar value (e.g. from Context) into a string.<br>
Actually the provided class _NonScalarJsonEncode_ use json_encode and prefix it with the attribute name.<br>
<br>
The NonScalarConverter is used, when a formatter-line includes a nonScalar and a scalar content.<br>

_example of usage_:<br>
```
use Terrazza\Component\Logger\Converter\NonScalar\NonScalarJsonConverter;

$record = ["message" => "myMessage", "key" => ["value1", "value2"]];
echo (new NonScalarJsonEncode())->getValue($context["key"]); // key:{"value1", "value2"}

// in context of the formatter it will be
$format = ["{Message}:{Context.key}"];                 // ... myMessage:key:{"value1", "value2"}
$format = ["Context" => "{Message}:{Context.key}"];    // ... myMessage:key:{"value1", "value2"}
```
a ValueConverter (LogRecordValueConverterInterface) can be used to convert special value based on his key.

#### method: pushConverter
to map/convert a special value, based on his key, push a special converter.<br>
This converter has to fulfill LogRecordValueConverterInterface. 

#### method: formatRecord
Maps the Record against the $format and returns a mapped array.<br>
Unknown patterns (e.g. {undefined}) are removed from the response.<br>
Empty "Lines" are also removed.

##### example<br>
The example uses an additional ValueConverter for the Record value "Date".
````
use DateTime;
use Terrazza\Component\Logger\LogRecordValueConverterInterface;
use Terrazza\Component\Logger\LOgRecord;
use Terrazza\Component\Logger\Formatter\LogRecordFormatter;
use Terrazza\Component\Logger\Converter\NonScalar\NonScalarJsonEncode;

class RecordTokenValueDate implements LogRecordValueConverterInterface {
    private string $dateFormat;
    public function __construct(string $dateFormat="Y-m-d H:i:s.u") {
        $this->dateFormat                           = $dateFormat;
    }
    public function getValue($value) {
        return $value->format($this->dateFormat);
    }
}

$formatter = new LogRecordFormatter(
   new NonScalarJsonEncode,
   ["Date", "Message"]
);
$formatter->pushConverter("Date", new RecordTokenValueDate);   

$record  = LogRecord::create("LoggerName", 100, "myMessage");
var_dump($formatter->formatRecord($record)); 
/*
[
   "Date" => 2022-12-31 23:59:01,
   "Message" => "myMessage"
]
*/
````
<a id="object-log-handler-filter" name="object-log-handler-filter"></a>
<a id="user-content-object-log-handler-filter" name="user-content-object-log-handler-filter"></a>
### LogHandlerFilter
[LogHandler](object-log-handler) can have a LogHandlerFilter.<br>
Properties:
- include (array, optional)
- exclude (array, optional)
- start (array, optional)
#### method: isHandling (string $callerNamespace) : bool
**include**<br>
preg_match callerNamespace against include patterns<br>
**exclude**<br>
preg_match callerNamespace against exclude patterns<br>
**start**<br>
preg_match callerNamespace against start patterns<br>
if preg_match is true all further isHandling will be true.
_(exclude filter overrules start)_

<a id="object-converter" name="object-converter"></a>
<a id="user-content-object-converter" name="user-content-object-converter"></a>
### Converter
A [LogRecord](#object-record) will be first converted/mapped with the [RecordFormatter](#object-formatter)<br>
Afterwards, it depends on the target/writer the array has to be formatted again.<br><br> 
We actually include/provide two Converter:
- convert to a string, json type
- convert to a string, e.g. for console logging
In any case the Converter is injected into the [Writer](#object-writer-stream).

#### FormattedRecordFlat
Converts the mapped LogRecord into a string within a delimiter for each row.<br>
For nonScalar values we use json_encode to convert the value.<br>
##### method: setNonScalarPrefix(string $delimiter)
by using this method nonScalar values will be prefixed with the dataKey and the delimiter.<br>
arguments:
- delimiter (string, required)
- encodingFlags (int, optional)

_example of usage_:<br>
```
use Terrazza\Component\Logger\Converter\FormattedRecord\FormattedRecordFlat;

$formatter = new FormattedRecordFlat("|",0);
echo $formatter->convert(["message" => "myMessage", "context" => ["k" => "v"]); 
//myMessage|{"k":"v"}

$formatter->setNonScalarPrefix(":");
echo $formatter->convert(["message" => "myMessage", "context" => ["k" => "v"]); 
//myMessage|context:{"k":"v"}
```

#### FormattedRecordJson
Converts the mapped LogRecord into a string by using json_encode.<br>
arguments:
- encodingFlags (int, optional)

_example of usage_:<br>
```
use Terrazza\Component\Logger\Converter\FormattedRecord\FormattedRecordJson;

$formatter = new FormattedRecordJson(0);
echo $formatter->convert(["message" => "myMessage", "context" => ["k" => "v"]); 
//{"message" : "myMessage", "context": {"k":"v"}}
```
### Writer

<a id="object-writer-stream-file" name="object-writer-stream-file"></a>
<a id="user-content-object-writer-stream-file" name="user-content-object-writer-stream-file"></a>
#### StreamFile
Save converted record to a file.<br>
arguments:
- converter (IFormattedRecordConverter, required)
- filename (string required)
- flags (int, optional, default: 0)

>the converter should convert the formatted LogRecord into a string. 
 
```
use Terrazza\Component\Logger\Writer\StreamFile;
use Terrazza\Component\Logger\Converter\FormattedRecord\FormattedRecordFlat;

$logFile    = "log.txt";
@unlink($logFile);
$converter  = new FormattedRecordFlat("|",0);
$writer     = new StreamFile($converter, $logFile);
$writer->write(["message" => "myMessage", "context" => ["k" => "v"]);

$logContent = file_get_contents($logFile);
echo $logContent;
//{"message" : "myMessage", "context": {"k":"v"}}
```

<a id="install" name="install"></a>
<a id="user-content-install" name="user-content-install"></a>
## How to install
### Install via composer
```
composer require terrazza/logger
```
<a id="require" name="require"></a>
<a id="user-content-require" name="user-content-require"></a>
## Requirements
- php >= 7.4
### composer packages
- psr/log

<a id="examples" name="examples"/></a>
<a id="user-content-examples" name="user-content-examples"/></a>
## Examples
### 1. create a ChannelHandler
```
use Terrazza\Component\Logger\Converter\FormattedRecord\FormattedRecordFlat;
use Terrazza\Component\Logger\Writer\StreamFile;
use Terrazza\Component\Logger\Formatter\RecordFormatter;
use Terrazza\Component\Logger\Converter\NonScalar\NonScalarJsonEncode;
use Terrazza\Component\Logger\Handler\ChannelHandler;

$writeConverter   = new FormattedRecordFlat("|",0); 
$writer           = new StreamFile($writeConverter, "test.log");
$formatter        = new RecordFormatter(new NonScalarJsonEncode(), [
   "Message" => "{Level}-{Message}-{Context.pid}"
]); 
$channelHandler   = new ChannelHandler($writer, $formatter);
```
#### 2. create a LogHandler
>notice<br>
next code lines depends on previous example... (create a ChannelHandler)
```
use Terrazza\Component\Logger\Handler\LogHandler;
use Terrazza\Component\Logger\Logger;

$handler          = new LogHandler(Logger::WARNING);
// push handler into previouse create channelHandler
$channelHandler->pushLogHandler($handler);
```
### 3. create within a handler
>notice:<br>
next code lines depends on previous example... (create a LogHandler)
```
use Terrazza\Component\Logger\Logger;

// additinal we initialize the Context with pid = getmypid
// the formatter uses {Context.pid} and will print it

$logger           = new Logger("loggerName", ["pid" => "myPID"], $channelHandler);
$logger->error($message = "message");
```
### create and registerChannelHandler
```
use Terrazza\Component\Logger\Logger;

$logger           = new Logger("loggerName", ["pid" => getmypid()]);
$logger           = $logger->registerChannelHandler($channelHandler);
$logger->error($message = "message");
```
### Usage of Logger
#### produce a NOTICE
```
$logger->notice("myMessage");
// by using our examples above this message will not be printed
// ...cause the logLevel for the Handler is WARNING
```

#### produce an ERROR
```
$logger->error("myMessage"); 
// output to file will be: 400-myMessage-myPID
```