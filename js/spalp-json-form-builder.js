/**
 * @file
 * Configuration form builder implementation.
 */
(function ($, Drupal, drupalSettings) {

    'use strict';

    /**
     * Builds json form based on schema.
     * @type {{attach: Drupal.behaviors.spalp_json_form_builder.attach}}
     */
    Drupal.behaviors.spalp_json_form_builder = {
        attach: function (context, settings) {
            if (typeof settings.spalpJsonFormBuilder != 'undefined' && settings.spalpJsonFormBuilder != null) {
                $.each(settings.spalpJsonFormBuilder, function (key, jsonFormElement) {
                    if (schemaDefined(jsonFormElement)) {
                        $("#"+jsonFormElement.identifier, context).once('spalp_json_form_builder').each(function() {
                            const brutsin_forms = brutusin["json-forms"];
                            const container = document.getElementById(jsonFormElement.identifier);
                            const bf = brutsin_forms.create(jsonFormElement.schema);
                            const data = $(jsonFormElement.textarea).val() || '{}';
                            bf.render(container, JSON.parse(data));
                            const event_data = {"brutsin_form": bf, "json_config_field": jsonFormElement.textarea};
                            $("#" + jsonFormElement.identifier + " input").on('change', event_data, function(event){
                                $(event.data.json_config_field).val(JSON.stringify(event.data.brutsin_form.getData()));
                            });
                        });
                    }
                });
            }
        }
    };

    /**
     * Helper function to check if element has schema attached.
     *
     * @param array settings
     *
     * @returns {boolean}
     *   True if schema and identifier is available in settings.
     */
    function schemaDefined(settings) {
        return typeof (settings.schema) != 'undefined' && settings.schema != null
            && typeof (settings.identifier) != 'undefined' && settings.identifier != null;
    }
})(jQuery, Drupal);