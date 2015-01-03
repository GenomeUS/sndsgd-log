# Examples

### Creating a Log Record

```php
use \sndsgd\log\Record as LogRecord;

# shorthand syntax
$record = LogRecord::create('example', 'testing one, two, three');

# longhand
$record = new LogRecord;
$record->setName('example');
$record->setMessage('testing one, two, three');

# longhand + method chaining
$record = (new LogRecord)
   ->setName('example')
   ->setMessage('testing one, two, three');
```

### Adding data to a log record

Given that ```sndsgd\log\Record``` utilizes the ```sndsgd\util\data\Manager``` trait, you can set, add, remove, or get data from a log record.

```php
$record = LogRecord::create($logName, $logMessage);
$record->addData('server', $_SERVER);
```

### Writing a log record

To *write* a log record, just pass the name(s) of one or more subclass(es) of ```sndsgd\log\Writer``` to the ```write``` method on a record instance.

```php
# write the log record to a file
$record->write('sndsgd\\log\\file\\Writer');

# write to a file, and then to an email using Mailgun
$record->write('sndsgd\\log\\file\\Writer', 'sndsgd\\log\\mailgun\\Writer');
```

You may find it easier to define constants for the various log writer classes you plan to use in your project config script.

```php
const LOG_TO_FILE = 'sndsgd\\log\\file\\Writer';
const LOG_TO_EMAIL = 'sndsgd\\log\\mailgun\\Writer';
```

```php
LogRecord::create('login-failure', 'A user failed to login')
   ->setData([
      'username' => $_POST['username'],
      'ip address' => $_SERVER['REMOTE_ADDR']
   ])
   ->write(LOG_TO_FILE);
```
