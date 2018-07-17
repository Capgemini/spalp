<?php

/**
 * @file
 * Hooks specific to the spalp module.
 */

/**
 * @addtogroup hooks
 */

/**
 * Alter app IDs.
 *
 * @param array $ids
 *   The available app IDs.
 */
function hook_spalp_app_ids_alter(&$ids) {
  $ids[] = 'mymodule_app_id';
}
