# Single Page App Landing Page module for Drupal

This module provides a standardised way for site managers to configure and serve
single-page applications as pages in a Drupal site.

For more information on the thinking behind this module, see
https://capgemini.github.io/drupal/spalp/

This approach has been described as “Progressively Decoupled”
- see https://dri.es/how-to-decouple-drupal-in-2018

## Installation

### Using Composer
1. Include the following lines in the `repositories` section
 of your project's `composer.json`:

        {
            "type": "package",
            "package": {
                "name": "brutusin/json-forms",
                "version": "0.0.0",
                "type": "drupal-library",
                "source": {
                    "url": "https://github.com/brutusin/json-forms.git",
                    "type": "git",
                    "reference": "origin/master"
                }
            }
        }
2. Run `composer require brutusin/json-forms`

### Without Composer
1. Download the JSON Forms library from https://github.com/brutusin/json-forms
2. Extract it into your site's `web/libraries` directory

## App Landing Page content type
The module defines a content type to provide a landing page for each app.

All relevant configuration and text used in the app is stored on this node,
and made available as a JSON endpoint to be consumed by the app.

## JSON endpoints
When viewing an applanding node, a link to the JSON endpoint will appear
 in the page head, with the id `appConfig`.

Your JavaScript application should get its config from the endpoint.

TODO: info on URLs, translations, revisioning

## Extending the module
See the spalp_example module for a simple implementation.

Create a module that implements an EventSubscriber on the
 `SpalpAppIdsAlterEvent::APP_IDS_ALTER` event.
The EventSubscriber should provide the module's app id.
See \Drupal\spalp_example\EventSubscriber\SpalpExampleAppIdsAlterSubscriber

The app ID provided by your module will be used as the ID of
a <div> element on the node view.
This can be used as your main app element.

### Default configuration and application text
Create a JSON file in your module directory called `config/spalp/mymodule.config.json`.
See [spalp_example.config.json](https://git.drupalcode.org/project/spalp/blob/8.x-1.x/spalp_example/config/spalp/spalp_example.config.json) for an example of the structure.

When your module is installed, an unpublished `applanding` node will be created,
with the module name selected as the `field_spalp_app_id` value.

`appConfig` and `appText` values will be stored on the node's
 `field_spalp_config_json` field.
 
Generate a JSON schema file at `config/spalp/mymodule.config.schema.json`. This is used by the [JSON Forms library](https://github.com/brutusin/json-forms) to build an editing form for the JSON field. 


### Adding your app's assets
Define a library for your assets as per https://www.drupal.org/node/2274843.
If the library name matches your module's machine name, the spalp module
will take care of attaching the library when the app landing node is viewed.

### Using config in Drupal code
It may be useful to access config and text in applanding nodes from elsewhere in Drupal, such as a block linking to the applanding node. This can be achieved using the `spalp.core` service:

    $config = \Drupal::service('spalp.core')->getAppConfig('mymodule');
    $name = $config['appText']['name'];

See `\Drupal\spalp_example\Plugin\Block\ExampleBlock::build` for an example.

## Known Issues

### Fatal error while creating app landing page content
It is necessary to apply this patch to Drupal core:
 https://www.drupal.org/files/issues/2018-05-17/2599228-51.patch

See https://www.drupal.org/project/drupal/issues/2599228 and
