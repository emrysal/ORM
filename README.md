# Release 1.0.0

## What is this?

This is a toolkit to perform Object Relational Mapping (ORM) for PHP. It is based on the popular ORM design pattern ActiveRecord.

## Requirements

This ORM requires at least PHP 5.3 and a working PDO database connection running a MySQL database. Support for other specifications may be enabled in the future. 

## How do I use this?

Currently, configuration options are yet to be enabled, but you can try this toolkit by configuring lib/Table.php to your connection. That'd be all there is to it.

## Examples

### Inserting
```php
$example = new Example();
$example->table_field = 'value';
$example->save();
```

### Updating
```php
$example = Example::find(1);
$example->table_field = 'value';
$example->save();
```

### Selecting
```php
$example = Example::find(array('table_field' => 'value'));
```

### Deleting
```php
$example = Example::find(1);
$example->delete();
```

## License

> Copyright (C) 2012, Alex van Andel
>
> Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, pulverize, distribute, synergize, compost, defenestrate, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
>
> The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
>
> If the Author of the Software (the "Author") needs a place to crash and you have a sofa available, you should maybe give the Author a break and let him sleep on your couch.
>
> If you are caught in a dire situation wherein you only have enough time to save one person out of a group, and the Author is a member of that group, you must save the Author.
>
> THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO BLAH BLAH BLAH ISN'T IT FUNNY HOW UPPER-CASE MAKES IT SOUND LIKE THE LICENSE IS ANGRY AND SHOUTING AT YOU.