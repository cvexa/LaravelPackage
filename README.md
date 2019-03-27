# Project Title

Search task<br/>
<i>Searching in application root folder/folders in all files filtered by a page inputs, radio buttons and select</i>

## Getting Started

These instructions will get you a copy of the package on your machine for development and testing purposes.


### Prerequisites

Laravel ^5.6*<br/>
orchestra/testbench-dusk ~3.4<br/>


### Installing

download and install the package via composer

```
composer require cvexa/finder
```
if 'stability errors' run

```
composer require "cvexa/finder:dev-master"
```
if 'links are not supported error' run
```
php artisan storage:link
```
package will install orchestra/testbench-dusk package if not presented<br/>
if package its not installed previously run the following comand in order to run the tests later<br>

```
php artisan dusk:install
```

publish package Tests

```
php artisan vendor:publish \\select Provider: cvexa\finder\FinderServiceProvider
```

searchTest.php will be copied to Tests\Browser;

## Package routes

```
GET /cvexa/find
```

## Description

Input for keyword/sentence validation for max:500 characters, this field is CASE SENSITIVE, and the search is CASE SENSITIVE<br/>
File extensions section validation for max:500 characters<br/>
extenstions can be listed with dot, or without (.pdf, pdf) are both valid,<br/>
more than one can be listed in the input separeted by a comma, for example:<br/>
pdf, .php, .txt, css ,etc.<br/>
extensions who are skipped on CONTENT search in files:<br/>
Microsoft Office files (for example ):
```
*.doc
*.docx
*.xls
*.ppt
*....
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

<b>Places 3 options</b><br>
<i>every option eliminate the other 2, and all options can be empty (except the select - default is all) if no option is changed(or left empty), the public folder select 'all' will be used to search in</i><br/>
<b>1.Custom path application</b><br/>

denied permission for '/' and '/vendor', 'vendor' paths, this field is for searching in the <i>root/ folders<br/> of the laravel application</i>, for example '/app' will search in Laravel/app folder, /routes will search in Laravel/routes<br/>
<br/>

<b>2.Public Folder browser</b><br/>
with select can choose between all folders in the <i>Laravel/public</i> folder they will be listed, if all value is selected<br/> will search in the whole public folder<br/>

<b>3.Custom path outside the application folder (one level above)</b>
search in all files and folders outside the application folder for example:
server/<br/>
&nbsp;&nbsp;/folder1<br/>
&nbsp;&nbsp;&nbsp;/folder1.1<br/>
&nbsp;&nbsp;&nbsp;etc..<br/>
&nbsp;&nbsp;/folder2<br/>
&nbsp;&nbsp;/folder3<br/>
&nbsp;&nbsp;/Laravel(with the package)<br/>
&nbsp;&nbsp;/folder5<br/>
&nbsp;&nbsp;file.txt<br/>
&nbsp;&nbsp;etc..<br/>

<b>Search Filter</b><br/>
determinate to search by a file Name or file Content (by default by Name)<br/>

<b>Priority:</b><br/>
filter<br/>
extensions input<br/>
one of the 3 search path methods<br/>

<b>Result box</b><br/>
will appear after every search to show time consumed doing the search, and listing the results if any<br/>
with the path to file where match is found<br/>

<b>Searching will be performed also on all subfolders, provided by custom paths</b>
## Running the tests

note the application must be running while starting the tests !
dusk testing url is configured in .env file for example:
```
APP_URL=http://localhost:8000
```
more about [Laravel Dusk](https://laravel.com/docs/5.8/dusk)

```
php artisan dusk
```
This command will run all dusk tests, of the application including now the package tests<br>
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

comment '--headless', to open browser and see realtime browser test
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
after that are some tests for searching in:<br/>
web.php<br/>
api.php<br/>
app.css<br/>
app.js<br/>
and searching for 'Controller' by content and by filename in<br/>
/app/Http/Controllers/<br/>
and ending with validation tests for path to search for '/' and '/vendor' and 'vendor'<br/>
and waiting for error<br/>
assertions are 22 that must be successfull to pass the test.<br/>

## Authors

* **Svetoslav Vasilev** - [GIT](https://github.com/cvexa)
