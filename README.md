# deckBuilder
If you want to create a custom deck of cards and know HTML, CSS & basics of PHP - deckBuilder is a perfect tool for you!
Just fill the CSV file with the data you'll put on the cards, then create a card template.


---
# Setting up
```php
require_once 'deckBuilder.php';
$deck = new deckBuilder;
// Language of the deck
$deck->lang = 'EN';

/**
* To change settings just uncomment a line and replace default value:
*/
// Rename type of the deck (can be used for styling)
// $deck->type = 'default';

// Set custom path to the template directory
// $deck->tplDir = 'tpl/';

// Card template file (without .tpl extension)
// $deck->cardTpl = 'card';

// Default field template file (without .tpl extension)
// $deck->fieldDefTpl = 'fieldDef';

$deck->loadSheet('Example deck data.csv');
echo $deck->render();
```


---
# Basics
##CSV Files
deckBuilder uses CSV (UTF-8 encoding, comma separated) files as database.
Values in the first row (header row) are used as a field names.
See example file: ``Example deck data.csv``.

###Google Docs
You can easily use spreadsheet from a Google Docs (Google Sheets).
To do so open the sheet in Google Docs, copy the download link and use it as a file path:
 ``File`` -> ``Download as`` -> ``Comma-separated values`` 
Check this [example Google Sheet](https://docs.google.com/spreadsheets/d/1dIJYT-7GwbX3SKnIa52gBdmc58AdB-qtEoMjCxZLDWQ/edit?usp=sharing) to test it out.

Example code:
```php
$deck = new deckBuilder;
$deck->loadSheet('https://docs.google.com/spreadsheets/u/0/d/1dIJYT-7GwbX3SKnIa52gBdmc58AdB-qtEoMjCxZLDWQ/export?format=csv&id=1dIJYT-7GwbX3SKnIa52gBdmc58AdB-qtEoMjCxZLDWQ&gid=0');
```


---
# Templates
Templates are php files with the `.tpl` extension that returns a string value.
By default templates are located in the `tpl/` directory; this can be changed by altering the `$tplDir` param.
Two types of templates are used: 
- **Card template**: template of the whole card;
- **Field template**: template of a single type of field that can be used in the card template. 
You can use `$this->type` and `$this->lang` in both of them to print the type/language of the current deck.
See directory `exampleTpl` for examples.

## Functions
Following functions are avaliable in the both types of templates:
-`$this->safeChars ($string)` trims the string and transliterates non-ASCII characters.
-``$this->stringToFilename ($string)`` transforms string into filename-safe form (safeChars + removes characters other than alphanumeric, dash, underscore and dot and limits the string length to 250 characters).
-`$this->stringToSelector ($string)` removes characters that aren't allowed in a CSS selector.

## Field Template
In the field templates following variables are avaliable:
- **$fieldName** - name of the field (from the header row).
- **$fieldClass** - name of the field.
- **$fieldText** - text from the field. In the default  `renderType` (`html`) special characters are converted to HTML entities and HTML line breaks are inserted before all newlines.
- **$fieldTextHTML** - avaliable only in the default  `renderType` (`html`). It returns text from the field as is so HTML code can be used inside those fields.

---
Example Field Template - **fieldDef**
```php
<?php
return <<<TPL
<span class="{$fieldClass}">{$fieldText}</span>
TPL;
?>
```
Returns filtered text inside of a `span` element.

---
Example Field Template - **ImagePng**:
```php
<?php
return <<<TPL
<img src="img/{$this->stringToFilename($fieldText)}.png" class="ImagePNG {$fieldClass}"/>
TPL;
?>
```
This template will use field text as filename and name of the field as a class.

---
Example Field Template - **NoFiltering**:
```php
<?php return $fieldTextHTML; ?>
```
This template will return value of a field as-is, without any filtering of any kind so HTML code can be .

## Card Template
Card Template is a template of an entire card.

The folllowing function can be called to use a Field Template inside of a Card Template:
``$this->field ($fieldName, $fieldTpl)``
- **$fieldName**: name of the field without language suffix.
Example: if in the sheet there are fields `Title EN` and `Title PL` simply use `Title` as a field name. The suffix will be added automatically depending on the `lang` param of the deck.
If in the sheet there's a field with and a field without language suffix (e.g. `Title EN` and `Title`), the later one will be used.
- **$fieldTpl** (_optional_): name of the field template file (without `.tpl` extension). If not set default template (`fieldDef` by default) will be used.

###Example Card Template:
``` php
<?php
return <<<TPL
<div class="Card type_{$this->type} lang_{$this->lang}">
{$this->field('Title')}
{$this->field('Power', 'NoFiltering')}
{$this->field('Title EN', 'ImagePng')}
</div>
TPL;
?>
```
In this simple example:
- a filtered Title of the card will be displayed using default template (`fieldDef`).
- a non-filtered Description will be displayed using `NoFiltering` template.
- an image with filename generated from field `Title EN` will be displayed (even if the `lang` is set to other language).

Let's say this is the CSV file and `lang` is set to `PL`:
| Title EN | Title PL | Power |
|-|-|-:|
| Golden Hammer! | Złoty Młot! | 100% |

On render following code will be outputted:
``` html
<div class="Card type_default lang_PL">
<span class="Title PL">Złoty Młot!</span>
100%
<img src="img/Golden_Hammer.png" class="ImagePNG Title EN"/>
</div>
```

---
# Printing
Use your web browser to send the deck directly to your printer or to a PDF file.
Support for rendering PDF files directly is planned for a future release of deckBuilder. 

Remember: for the best result use print units (cm, mm, in, pt, pc) instead of px in a stylesheet.

## Firefox
`File` > `Print preview` > `Page setup`
In `Format & Options` check `Print background (colors & images)` and set scale to 100%
In `Margins & Header/Footer` set margins as you need and remove unwanted headers and footers.

## Chrome
`Menu` > `Print...`  > `More settings`:
- Check option `Background graphics` ;
- Uncheck option `Headers and footers`;
- Change `Color` from `Black & White` to `Color`;
- Set `Scale` to `100`;
- Change margins to your needs.


---
# Credits
**deckBuilder copyright © 2017 [lukasz2049](https://github.com/lukasz2049)**
deckBuilder uses [parseCSV](https://github.com/parsecsv/parsecsv-for-php) library by Jim Myhrberg under the MIT license
