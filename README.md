Markdown Editor and DBField
=================

Adds a field and a data type that allows for Markdown editing, uses a supported renderer (default is the github api) to render the html

## Requirements
* SilverStripe 3.x
* PHP Curl Support

## Installation
* Download the module from here https://github.com/UndefinedOffset/silverstripe-markdown/downloads
* Extract the downloaded archive into your site root so that the destination folder is called markdown, opening the extracted folder should contain _config.php in the root along with other files/folders
* Run dev/build?flush=all to regenerate the manifest
* Upon entering the cms and using MarkdownEditor for the first time you make need to add ?flush=all to the end of the address to force the templates to regenerate

## Usage
Use the Markdown data type as your fields data type, then use the MarkdownEditor field in the cms for editing.

###Page class:
```php
class MyPage extends Page {
    public static $db=array(
                            'MarkdownContent'=>'Markdown'
                        );
    
    public function getCMSFields() {
        $fields=parent::getCMSFields();
        
        $editor = new MarkdownEditor('MarkdownContent', 'Page Content (Markdown)');
        $editor->setRows(15); //optional, set number of rows in CMS
        $editor->setWrapMode(true); //optional, turn on word wrapping
        $fields->addFieldToTab("Root.Main", $editor);
        
        return $fields;
    }
}
```


###Template:
```html
<div class="content">
    $MarkdownContent  <!-- Will show as rendered html -->
</div>
```

You may also request the markdown using Github Flavored Markdown by calling $YourField.AsHTML(true) in your template by default Github Flavored Markdown is not used just regular Markdown is used.
```html
<div class="content">
    $MarkdownContent.AsHTML(true)  <!-- Will render the content using Github Flavoured Markdown -->
</div>
```

###Configuration:
The default renderer is the Github renderer. However, other renderers are supported.

To set what renderer to use, in **_config.php** do the following:

```php
Markdown::setRenderer('GithubMarkdownRenderer'); //Class name of any implementation of IMarkdownRenderer will work
```

####GithubMarkdownRenderer
The following options are available on the default GithubMarkdownRenderer:
```php
GithubMarkdownRenderer::useBasicAuth('github username', 'github password'); //authenticate to the Github API to get 5,000 requests per hour instead of 60
GithubMarkdownRenderer::setUseGFM(true); //whether or not to use Github Flavoured Markdown
```

####PHPMarkdownMarkdownRenderer
PHPMarkdownMarkdownRenderer is simple and has no options. Use this to avoid the delay on page load the first time after editing that comes from using the Github renderer (especially if the page has many sections of markdown). You will need to install [PHP Markdown](https://github.com/michelf/php-markdown) for this to work.

**Note:** This renderer does not support Github Flavoured Markdown.
```php
$renderer=new PHPMarkdownMarkdownRenderer();
Markdown::setRenderer($renderer);
```