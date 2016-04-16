# Running The Propel Unit Tests #

## Background ##

Propel uses [PHPUnit](http://www.phpunit.de) to test the build and runtime frameworks.

You can find the unit test classes and support files in the `test/testsuite` directory.

## Quick Guide to Run Tests (using MySQL) ##
### Install PHPUnit ###

In order to run the tests, you must install PHPUnit:
```
pear channel-discover pear.phpunit.de
pear install phpunit/PHPUnit
```

### Setup the MySQL databases to be used in the tests ###

All databases used by tests are are prefixed with `propel1_test_`.
Tests connect with user `propel1_test` to localhost, without a password.

Following SQL script will create all necessary test databases and grant
access to the user `propel1_test`, so the unit tests should pass without any
special configuration:

```sql
CREATE USER 'propel1_test'@'localhost';
GRANT ALL ON `propel1\_test\_%`.* TO 'propel1_test'@'localhost';

CREATE DATABASE propel1_test_bookstore;
CREATE DATABASE propel1_test_bookstore_namespaced;
CREATE DATABASE propel1_test_reverse_bookstore;
CREATE DATABASE propel1_test_schema_bookstore;
CREATE DATABASE propel1_test_schema_contest;
CREATE DATABASE propel1_test_schema_second_hand_books;
```

### Rebuild object models and create tables in the database ###
Run `/path/to/propel/test/reset_tests.sh`.

### Run PHPUnit ###
In Propel root directory run `phpunit`.

## Details ##

Specific tests requires specific Propel models to be built and configured
(in `test/fixtures/`). If there is no
`test/fixtures/<name>/build/conf/<name>-conf.php` units requiring the fixture
are skipped.

### Configure the database to be used in the tests ###

If you want to use different database or MySQL user,
you must configure both the generator and the runtime connection settings.

I.e. for test using bookstore model:

In `test/fixtures/bookstore/build.properties`:
```ini
propel.database = mysql
propel.database.url = mysql:dbname=propel1_test_bookstore
propel.mysqlTableType = InnoDB
propel.disableIdentifierQuoting=true
# For MySQL or Oracle, you also need to specify username & password
propel.database.user = propel1_test
propel.database.password = p@ssw0rd
```

In `test/fixtures/bookstore/runtime-conf.xml`:
```xml
<datasource id="bookstore">
  <!-- the Propel adapter to use for this connection -->
  <adapter>mysql</adapter>
  <!-- Connection parameters. See PDO documentation for DSN format and available option constants. -->
  <connection>
      <classname>DebugPDO</classname>
      <dsn>mysql:dbname=propel1_test_bookstore</dsn>
      <user>propel1_test</user>
      <password>p@ssw0rd</password>
      <options>
        <option id="ATTR_PERSISTENT">false</option>
      </options>
      <attributes>
        <!-- For MySQL, you should also turn on prepared statement emulation,
                        as prepared statements support is buggy in mysql driver -->
        <option id="ATTR_EMULATE_PREPARES">true</option>
      </attributes>
      <settings>
        <!--  Set the character set for client connection -->
        <setting id="charset">utf8</setting>
      </settings>
  </connection>
</datasource>
```

**Tip**: To run the unit tests for the namespace support in PHP 5.3,
you must also configure the `fixtures/namespaced` project.

**Tip**: To run the unit tests for the database schema support,
you must also configure the `fixtures/schemas` project.
This project requires that your database supports schemas,
and already contains the following schemas:
- `propel1_test_schema_bookstore`
- `propel1_test_contest`
- `propel1_test_second_hand_books`

Note that the user defined in `build.properties` and `runtime-conf.xml` must
have access to these schemas.

### Build the Propel Model and Initialize the Database ###

```
cd /path/to/propel/test
../generator/bin/propel-gen fixtures/bookstore main
mysqladmin create test
../generator/bin/propel-gen fixtures/bookstore insert-sql
```

**Tip**: To run the unit tests for the namespace support in PHP 5.3,
you must also build the `fixtures/namespaced` project.
**Tip**: To run the unit tests for the database schema support,
you must also build the `fixtures/schemas` project.

### Run the Unit Tests ###

Run all the unit tests at once using the `phpunit` command:
```
cd /path/to/propel/test
phpunit testsuite
```

'''Warning''': The `testsuite/generator/builder/NamespaceTest.php` file uses
PHP 5.3 namespaces, and therefore will create a parse error under PHP 5.2.
To launch the unit test suite in a PHP 5.2 platform, simply delete this test file.

To run a single test, go inside the unit test directory, and run the test using
the command line. For example to run only GeneratedObjectTest:

```
cd testsuite/generator/builder/om
phpunit GeneratedObjectTest
```

## How the Tests Work ##

Every method in the test classes that begins with 'test' is run as a test case
by PHPUnit. All tests are run in isolation; the `setUp()` method is called at
the beginning of ''each'' test and the `tearDown()` method is called at the end.

The `BookstoreTestBase` class specifies `setUp()` and `tearDown()` methods which
populate and depopulate, respectively, the database.  This means that every unit
test is run with a cleanly populated database.

To see the sample data that is populated, take a look at the
`BookstoreDataPopulator` class. You can also add data to this class, if needed
by your tests; however, proceed cautiously when changing existing data in there
as there may be unit tests that depend on it. More typically, you can simply
create the data you need from within your test method. It will be deleted by
the `tearDown()` method, so no need to clean up after yourself.

### Writing Tests ###

If you've made a change to a template or to Propel behavior, the right thing to
do is write a unit test that ensures that it works properly -- and continues to
work in the future.

Writing a unit test often means adding a method to one of the existing test
classes. For example, let's test a feature in the Propel templates that supports
saving of objects when only default values have been specified. Just add a
`testSaveWithDefaultValues()` method to the `GeneratedObjectTest` class, as follows:

```php
<?php
/**
 * Test saving object when only default values are set.
 */
public function testSaveWithDefaultValues() {

  // Relies on a default value of 'Penguin' specified in schema
  // for publisher.name col.

  $pub = new Publisher();
  $pub->setName('Penguin');
    // in the past this wouldn't have marked object as modified
    // since 'Penguin' is the value that's already set for that attrib
  $pub->save();

  // if getId() returns the new ID, then we know save() worked.
  $this->assertTrue($pub->getId() !== null, "Expect Publisher->save() to work  with only default values.");
}
```

Run the test again using the command line to check that it passes:

```
phpunit GeneratedObjectTest
```

You can also write additional unit test classes to any of the directories in
`test/testsuite/` (or add new directories if needed). The `phpunit` command will
find these files automatically and run them.
