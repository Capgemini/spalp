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
            $("#" + jsonFormElement.identifier, context).once('spalp_json_form_builder').each(function () {
              const brutusin_forms = brutusin["json-forms"];
              const container = document.getElementById(jsonFormElement.identifier);
              const brutusin_form_instance = brutusin_forms.create(jsonFormElement.schema);
              const data = $(jsonFormElement.textarea).val() || '{}';
              brutusin_form_instance.render(container, JSON.parse(data));
              $(jsonFormElement.textarea).hide();
              const event_data = {
                "brutusin_form": brutusin_form_instance,
                "json_config_field": jsonFormElement.textarea
              };
              $("#" + jsonFormElement.identifier + " input").on('change', event_data, function (event) {
                $(event.data.json_config_field).val(JSON.stringify(event.data.brutusin_form.getData()));
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