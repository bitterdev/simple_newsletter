/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */
(function () {

    $.getJSON(CCM_DISPATCHER_FILENAME + "/simple_newsletter/api/get_placeholders", function (placeholders) {
        CKEDITOR.config.newsletter_placeholders = placeholders;
    });

    CKEDITOR.config.str_insert_newsletter_placeholder_label = 'Placeholders';
    CKEDITOR.config.str_insert_newsletter_placeholder_title = 'Placeholders';
    CKEDITOR.config.str_insert_newsletter_placeholder_voice = 'Placeholders';

    CKEDITOR.plugins.add(
        'newsletterplaceholders',
        {
            requires: ['richcombo'],
            init: function (editor) {

                let config = editor.config;

                // add the menu to the editor
                editor.ui.addRichCombo('str_insert_newsletter_placeholder',
                    {
                        label: config.str_insert_newsletter_placeholder_label,
                        title: config.str_insert_newsletter_placeholder_title,
                        voiceLabel: config.str_insert_newsletter_placeholder_voice,
                        toolbar: 'insert',
                        className: 'cke_format',
                        multiSelect: false,
                        panel: {
                            css: [CKEDITOR.skin.getPath('editor')].concat(config.contentsCss),
                            multiSelect: false,
                            attributes: {'aria-label': 'My Dropdown Title'}
                        },

                        init: function () {
                            for (let key in CKEDITOR.config.newsletter_placeholders) {
                                let label = CKEDITOR.config.newsletter_placeholders[key];
                                let placeholder = "[[" + key + "]]";
                                this.add(placeholder, label);
                            }
                        },
                        onClick: function (value) {
                            editor.focus();
                            editor.fire('saveSnapshot');
                            editor.insertHtml(value);
                            editor.fire('saveSnapshot');
                        }
                    }
                );
            }
        }
    );

})();