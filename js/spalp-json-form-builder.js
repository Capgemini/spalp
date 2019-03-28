/**
 * @file
 * Configuration form builder implementation.
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  /**
   * Builds json form based on schema.
   *
   * @type {{attach: Drupal.behaviors.spalp_json_form_builder.attach}}
   */
  Drupal.behaviors.spalp_json_form_builder = {
    attach: function (context, settings) {
      if (typeof settings.spalpJsonFormBuilder != 'undefined' && settings.spalpJsonFormBuilder != null) {
        $.each(settings.spalpJsonFormBuilder, function (key, jsonFormElement) {
          if (schemaDefined(jsonFormElement)) {
            const jsonFormId = "#" + jsonFormElement.identifier;
            $(jsonFormId, context).once('spalp_json_form_builder').each(function () {
              const brutusin_forms = brutusin["json-forms"];
              const container = document.getElementById(jsonFormElement.identifier);
              const brutusin_form_instance = brutusin_forms.create(jsonFormElement.schema);
              const data = $(jsonFormElement.textarea).val() || '{}';
              const json_data = JSON.parse(data);

              // Set selected 'select' elements option from existing data.
              brutusin_forms.addDecorator(function (element, schema) {
                if (element.tagName) {
                  if (element.tagName.toLowerCase() === "select") {
                    const value = jsonValueFromPath(json_data, schema.$id);
                    $(element).val(value);
                  }
                }
              });

              // Replace the default text field with a generated form.
              brutusin_form_instance.render(container, json_data);
              $(jsonFormElement.textarea).hide();

              const event_data = {
                "brutusin_form": brutusin_form_instance,
                "json_config_field": jsonFormElement.textarea
              };

              // Update the default text field value when the generated form changes.
              $(jsonFormId).on('change', 'input, select', event_data, function (event) {
                updateJsonFormElementData(event.data);
              });

              // Update json element field when json form item is removed.
              $(jsonFormId + ' button.remove').on('click', event_data, function (event) {
                updateJsonFormElementData(event.data);
              });
            });
          }
        });
      }
    }
  };

  /**
   * Given an objectified JSON string, return a value for a given element path.
   *
   * @param json
   *   Object representing the JSON data.
   * @param path
   *   A dot separated path to the JSON data's element value required
   *
   * @return {string|int}
   *   Value of the JSON data specified in the path parameter.
   */
  function jsonValueFromPath(json, path) {
    const path_chunks = path.split('.');
    // The json-forms schema paths are prefixed with '$.'.
    const root = path_chunks.shift();

    return path_chunks.reduce(function(obj, key) {
      return obj && obj[key];
    }, json);
  }

  /**
   * Update textarea data with values from JSON form elements.
   *
   * @param {Object} event_data
   *   Data from the form change event.
   */
  function updateJsonFormElementData(event_data) {
    $(event_data.json_config_field).val(JSON.stringify(event_data.brutusin_form.getData()));
  }

  /**
   * Helper function to check if element has schema attached.
   *
   * @param {array} settings
   *
   * @returns {boolean}
   *   True if schema and identifier is available in settings.
   */
  function schemaDefined(settings) {
    return typeof (settings.schema) != 'undefined' && settings.schema != null
        && typeof (settings.identifier) != 'undefined' && settings.identifier != null;
  }
})(jQuery, Drupal);
