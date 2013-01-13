<?php
class MarkdownEditor extends TextareaField {
    protected $rows=30;
    
    /**
     * Returns the field holder used by templates
     * @return {string} HTML to be used
     */
    public function FieldHolder($properties=array()) {
        $this->extraClasses['stacked']='stacked';
        
        
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
                )
        );
    }
}
?>