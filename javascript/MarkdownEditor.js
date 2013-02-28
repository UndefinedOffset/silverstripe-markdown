(function($) {
    $.entwine('ss', function($) {
        $('textarea.markdowneditor').entwine({
            TextArea: null,
            Div: null,
            Editor: null,
            Frame: null,
            WrapMode: false,
            SoftTabs: true,
            onmatch: function() {
                $(this).setFrame({
                                width: '100%',
                                height: $(this).height()
                            });

                $(this).setTextArea($(this));
                $(this).hide();

                var div=$('<div id="'+$(this).attr('ID')+'_Editor" class="markdowneditor_editor"/>').css('height', $(this).getFrame().height).css('width', $(this).getFrame().width).text($(this).val());
                div.insertAfter($(this));
                $(this).setDiv(div);

                var editor=ace.edit(div.get(0));
                editor.getSession().setMode('ace/mode/markdown');
                editor.setShowPrintMargin(false);
                editor.setTheme($Theme);
                editor.resize();
                div.removeClass('ace_dark');
                $(this).setEditor(editor);

                var code=$(this).val();
                $(this).setUseSoftTabs($(this).usesSoftTabs(code));
                $(this).setTabSize($(this).getSoftTabs() ? $(this).guessTabSize(code):8);
                $(this).setUseWrapMode($(this).getWrapMode());
                $(this).setupFormBindings();
                $(this).setupHacks();
            },
            code: function() {
                return $(this).getEditor().getSession().getValue();
            },
            setupFormBindings: function() {
                var self=$(this);
                $(this).getEditor().getSession().on("change", function() {
                    self.getTextArea().val(self.code()).change();
                });
            },
            setupHacks: function() {
                $(this).getDiv().find('.ace_gutter').css("height", $(this).getFrame().height);
            },
            setUseSoftTabs: function(val) {
                $(this).setSoftTabs(val);
                $(this).getEditor().getSession().setUseSoftTabs(val);
            },
            setTabSize: function(val) {
                $(this).getEditor().getSession().setTabSize(val);
            },
            setUseWrapMode: function(val) {
                $(this).getEditor().getSession().setUseWrapMode(val);
            },
            guessTabSize: function(val) {
                var result=/^( +)[^*]/im.exec(val || $(this).code());
                return (result ? result[1].length:2);
            },
            usesSoftTabs: function(val) {
                return !/^\t/m.test(val || $(this).code());
            }
        });
    });
})(jQuery);