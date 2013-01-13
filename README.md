Markdown Editor and DBField
=================

Adds a field and a data type that allows for Markdown editing, uses the github api to render the html

## Requirements
* SilverStripe 3.x
* PHP Curl Support

## Installation
* Download the module from here https://github.com/UndefinedOffset/silverstripe-markdown/downloads
* Extract the downloaded archive into your site root so that the destination folder is called markdown, opening the extracted folder should contain _config.php in the root along with other files/folders
* Run dev/build?flush=all to regenerate the manifest
* Upon entering the cms and using MarkdownEditor for the first time you make need to add ?flush=all to the end of the address to force the templates to regenerate

## Usage
Use the Markdown data type as your fields data type, then use the MarkdownEditor field in the cms for editing. You may also request the markdown using Github Flavored Markdown by calling $YourField.AsHTML(true) in your template by default Github Flavored Markdown is not used just regular Markdown is used. Alternativly you can enable this site wide using Markdown::setUseGFM(true); in your _config.php