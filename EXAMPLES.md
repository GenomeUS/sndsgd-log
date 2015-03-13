# Examples

### Creating a Log Record

```php
use \sndsgd\log\Record as LogRecord;

# the default syntax
$record = new LogRecord;
$record->setName("example");
$record->setMessage("testing one, two, three");

# ...with method chaining
$record = (new LogRecord)
   ->setName("example")
   ->setMessage("testing one, two, three");

# shorthand syntax
$record = LogRecord::create("example", "testing one, two, three");
```


### Adding data to a log record

Given that ```sndsgd\log\Record``` utilizes the ```sndsgd\util\data\Manager``` trait, you can set, add, remove, or get data from any log record.

```php
$record = LogRecord::create($logName, $logMessage);
$record->addData("server", $_SERVER);
```


### Writing a log record

To *write* a log record, pass an instance(s) of ```sndsgd\log\Writer```, or the name(s) of one or more subclass(es) of ```sndsgd\log\Writer``` to the ```write``` method on a record instance.

```php
use \sndsgd\log\mailgun\Writer as MailgunWriter;

# providing a writer classname
$record->write("sndsgd\\log\\file\\Writer");

# providing a writer instance
# this is helpful when the writer has options
$writer = new MailgunWriter;
$writer->setSubject("ERROR!?!?!?!");
$writer->setRecipient("emergency.contact@domain.com");
$record->write($writer);

# note: you can write to multiple writers in one go
$record->write("sndsgd\\log\\file\\Writer", $writer);
```

