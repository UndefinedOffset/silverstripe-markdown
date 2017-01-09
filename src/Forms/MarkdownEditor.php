<?php

namespace UndefinedOffset\Markdown\Forms;

use SilverStripe\Forms\TextareaField;
use SilverStripe\View\Requirements;

class MarkdownEditor extends TextareaField {
    protected $rows=30;

    protected $wrap_mode=false;

    /**
     * Sets the "Wrap Mode" on the ACE editor markdown field.
     * @param boolean $mode True if word wrap should be enabled, false if not
     */
    public function setWrapMode($mode = false) {
        $this->wrap_mode=$mode;
        return $this;
    }

    /**
     * Returns the field holder used by templates
     * @return {string} HTML to be used
     */
    public function FieldHolder($properties=array()) {
        $this->extraClasses['stacked']='stacked';


        Requirements::css(MARKDOWN_MODULE_BASE.'/css/MarkdownEditor.css');

        Requirements::javascript(MARKDOWN_MODULE_BASE.'/javascript/external/ace/ace.js');
        Requirements::javascript(MARKDOWN_MODULE_BASE.'/javascript/external/ace/mode-markdown.js');
        Requirements::javascript(MARKDOWN_MODULE_BASE.'/javascript/external/ace/theme-textmate.js');
        Requirements::javascript(MARKDOWN_MODULE_BASE.'/javascript/external/ace/theme-twilight.js');
        Requirements::javascript(MARKDOWN_MODULE_BASE.'/javascript/MarkdownEditor.js');
        return parent::FieldHolder($properties);
    }

    /**
     * Generates the attributes to be used on the field
     * @return {array} Array of attributes to be used on the form field
     */
    public function getAttributes() {
        return array_merge(
                            parent::getAttributes(),
                            array(
                                'style'=>'width: 97%; max-width: 100%; height: '.($this->rows * 16).'px; resize: none;', // prevents horizontal scrollbars
                                'wrap-mode'=>($this->wrap_mode) ? "true" : "false"
                            )
                        );
    }
}
