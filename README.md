# Single Page App Landing Page module for Drupal

This module provides a way for site managers to configure and serve single-page applications as pages in a Drupal site.

This approach has been described as “Progressively Decoupled” - see https://dri.es/how-to-decouple-drupal-in-2018

The module defines an App Landing Page content type.

The standard view mode for these nodes would be the landing page for each app.

A separate view mode would provide a JSON endpoint for configuration and text, to be consumed by the app.

All relevant configuration would be stored on this node.


TODO: define app element ID to be replaced by JS - use the app ID for this?

## Extending the module
See the spalp_example module for a simple implementation.

Create a module that implements EventSubscriber to react on "SpalpAppIdsAlterEvent::APP_IDS_ALTER" event. EventSubscriber will provide module's app id to list of available app ids.

The app ID provided by your module will be used as the ID of a <div> element on the node view.
This can be used as your main app element.

### Default configuration and application text
Create a JSON file in your should be stored in mymodule.config.json.

When your module is installed, an applanding node will be created for the module.

The values of `appConfig` will be stored on the node's `field_spalp_app_config` field.
The values of `appText` will be stored on the node's `field_spalp_app_text` field.

See spalp_example.config.json for an example of the structure.

### Adding your app's assets
Define a library for your assets as per https://www.drupal.org/docs/8/creating-custom-modules/adding-stylesheets-css-and-javascript-js-to-a-drupal-8-module - the library name should match your module's machine name - the spalp module will then take care of attaching the library when the app landing node is viewed.
