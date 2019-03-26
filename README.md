# Project Title

Search task

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.


### Prerequisites

Laravel 5.*<br/>
orchestra/testbench-dusk ~3.4<br/>


### Installing

download and install the package via composer

```
composer require cvexa/finder
```

package will install orchestra/testbench-dusk package if not presented<br/>

publish package Tests

```
php artisan vendor:publish \\select Provider: cvexa\finder\FinderServiceProvider
```

searchTest.php will be copied to Tests\Browser;
## Package routes

```
Route::get('cvexa/find', 'FinderController@index')->name('find');
Route::get('cvexa/search', 'FinderController@search')->name('find.me');
```

## Description

Input for keyword/sentence validation for max:500 characters
File extensions section
more than one can be listed in the input separeted by a comma, for example:
pdf, .php, .txt, css.....
short on page description which extensions are skipped on CONTENT search in files, known skipped:
Microsoft Office files (for example ):
```
*.doc
*.docx
*.xls
*.ppt
```

valid content searchable CONTENT files:
```
*.php
*.txt
*.js
*.css
*.xml
*.env
*.pdf
*.scss
```


file extension filed can be empty, in that case all files extensions will be included in the search (except, for content search skipped extensions)<br/>
NO restriction on files to search by file NAME;<br/>

Custom path application<br/>
if this input is present(not empty), the public select folders will be ignored,<br/>
denied permission for '/' and '/vendor', 'vendor' paths, this field is for searching in the root/ folders<br/> of the laravel application, for example '/app' will search in Laravel/app folder, /routes will search in Laravel/routes<br/>
this field can be empty if so, will take the value of the next section dropdown.<br/>

Public Folder browser<br/>
with select can choose between all folders in the Laravel/public folder they will be listed, if all value is selected<br/> will search in the whole public folder<br/>

Search Filter<br/>
determinate to search by a file Name or file Content (by default by Name)<br/>

Priority:<br/>
filter<br/>
extensions input<br/>
custom path input<br/>
public folder browser<br/>

Result box<br/>
will appear after every search to show time consumed doing the search, and listing the results if any<br/>
with the path to file where match is found<br/>


## Running the tests

```
php artisan dusk
```
if you want to see the tests in browser open : tests/DuskTestCase.php<br/>
and find this method

```
protected function driver()
    {
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            '--headless',
            '--window-size=1920,1080',
        ]);

        return RemoteWebDriver::create(
            'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                $options
            )
        );
    }
```

comment '--headless', to open browser and see realtime brower test
```
protected function driver()
    {
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            <!-- '--headless', -->
            '--window-size=1920,1080',
        ]);

        return RemoteWebDriver::create(
            'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                $options
            )
        );
    }
```
### Testing

first test will visit the package url :/cvexa/find
and will wait for elements

```
$this->assertNotNull($browser->waitForText('Search in files'));
$this->assertNotNull($browser->element('#search'));
$this->assertNotNull($browser->element('#extensions'));
$this->assertNotNull($browser->element('#path'));
$this->assertNotNull($browser->element('#location'));
$this->assertNotNull($browser->element('#name'));
$this->assertNotNull($browser->element('#content'));
```

after that will click search button, and wait for validation errors

```
$this->assertNotNull($browser->element('.alert-danger'));
```
next thing is to search for 'Controller' in the public folder by file NAME, and wait for no result
```
$this->assertNotNull($browser->waitForText('No Results'));
```
next thing is to search for 'index' in the public folder by file NAME, and extension .php, waiting in result to present index.php
```
$this->assertNotNull($browser->waitForText('index.php'));
```
next search by content '$request = Illuminate\Http\Request::capture()'
```
$browser->type('#search', '$request = Illuminate\Http\Request::capture()');
$browser->radio('#content', '0');
$browser->click('#search-btn')
    ->pause(500);
$this->assertNotNull($browser->waitForText('index.php'));
```
after that are some tests for searching in:
web.php
api.php
app.css
app.js
and searching for 'Controller' by content and by filename in
/app/Http/Controllers/
and ending with validation tests for path to search for '/' and '/vendor' and 'vendor'
and waiting for error
assertions are 22 that must be successfull to pass the test.

## Authors

* **Svetoslav Vasilev** - [GIT](https://github.com/cvexa)
