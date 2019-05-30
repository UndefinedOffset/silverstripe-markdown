# Markdown Editor and DBField

Adds a field and a data type that allows for Markdown editing, uses a supported renderer (default is the GitHub API)
to render the HTML.

## Requirements

* SilverStripe 4.x
* PHP cURL Support

## Installation

* Install with [composer](https://getcomposer.org): `composer require undefinedoffset/silverstripe-markdown ^2.0`
* Run `dev/build?flush=all` to regenerate the manifest

## Usage

Use the Markdown data type as your fields data type, then use the MarkdownEditor field in the cms for editing.

### Page class

```php
use UndefinedOffset\Markdown\Forms\MarkdownEditor;

class MyPage extends Page
{
    public static $db = array(
        'MarkdownContent' => 'Markdown'
    );
    
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        
        $editor = new MarkdownEditor('MarkdownContent', 'Page Content (Markdown)');
        $editor->setRows(15); //optional, set number of rows in CMS
        $editor->setWrapMode(true); //optional, turn on word wrapping
        $fields->addFieldToTab('Root.Main', $editor);
        
        return $fields;
    }
}
```

### Template

```html
<div class="content">
    $MarkdownContent  <!-- Will show as rendered HTML -->
</div>
```

You may also request the markdown using Github Flavored Markdown by calling $YourField.AsHTML(true) in your template
by default Github Flavored Markdown is not used just regular Markdown is used.

```html
<div class="content">
    $MarkdownContent.AsHTML(true)  <!-- Will render the content using Github Flavoured Markdown -->
</div>
```

### Configuration

The default renderer is the GitHub renderer. However, other renderers are supported.

To set what renderer to use, in `_config.php` do the following:

```php
use UndefinedOffset\Markdown\Model\FieldTypes\Markdown;

// Fully qualified (namespaced) class name of any implementation of IMarkdownRenderer will work:
Markdown::setRenderer('UndefinedOffset\\Markdown\\Renderer\\GithubMarkdownRenderer'); 
```

#### GithubMarkdownRenderer

The following options are available on the default GithubMarkdownRenderer:

```php
use UndefinedOffset\Markdown\Renderer\GitHubMarkdownRenderer;

// authenticate to the Github API to get 5,000 requests per hour instead of 60
GithubMarkdownRenderer::useBasicAuth('github username', 'github password'); 
// whether or not to use Github Flavoured Markdown
GithubMarkdownRenderer::setUseGFM(true);
```

#### PHPMarkdownMarkdownRenderer

PHPMarkdownMarkdownRenderer is simple and has no options. Use this to avoid the delay on page load the first time
after editing that comes from using the Github renderer (especially if the page has many sections of markdown). You
will need to install [PHP Markdown](https://github.com/michelf/php-markdown) for this to work - it can be installed
with composer.

**Note:** This renderer does not support Github Flavoured Markdown.

```php
use UndefinedOffset\Markdown\Model\FieldTypes\Markdown;

Markdown::setRenderer('UndefinedOffset\\Markdown\\Renderer\\PHPMarkdownMarkdownRenderer');
```
