# Single Page App Landing Page module for Drupal

This module provides a standardised way for site managers to configure and serve
single-page applications as pages in a Drupal site.

This approach has been described as “Progressively Decoupled”
- see https://dri.es/how-to-decouple-drupal-in-2018

## App Landing Page content type
The module defines a content type to provide a landing page for each app.

All relevant configuration and text used in the app is stored on this node,
and made available as a JSON endpoint to be consumed by the app.

## JSON endpoints
When viewing an applanding node, a link to the JSON endpoint will appear in the page head, with the id `appConfig`:

    <link type="application/json" id="appConfig" rel="alternate" href="/spalp/spalp_example/json">


TODO: info on URLs, translations, revisioning

## Extending the module
See the spalp_example module for a simple implementation.

Create a module that implements EventSubscriber to react on "SpalpAppIdsAlterEvent::APP_IDS_ALTER" event. EventSubscriber will provide module's app id to list of available app ids.

The app ID provided by your module will be used as the ID of
a <div> element on the node view.
This can be used as your main app element.

### Default configuration and application text
Create a JSON file in your module directory called  mymodule.config.json.
See spalp_example.config.json for an example of the structure.

When your module is installed, an unpublished `applanding` node will be created,
with the module name selected as the `field_spalp_app_id` value.

`appConfig` and `appText` values will be stored on the node's `field_spalp_config_json` field.

### Adding your app's assets
Define a library for your assets as per https://www.drupal.org/node/2274843.
If the library name matches your module's machine name, the spalp module
will take care of attaching the library when the app landing node is viewed.

# Issues

Due to the content translation configuration to the content type(App landing page) created by spalp module, there will be a fatal error while we create App landing page content.
Please refer this link https://www.drupal.org/project/drupal/issues/2599228 .

Please apply the patch https://www.drupal.org/files/issues/2018-05-17/2599228-51.patch mentioned in https://www.drupal.org/project/drupal/issues/2599228 to get it working.
