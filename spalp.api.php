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
  // Your module should add its machine name to the $ids array.
  // This code is equivalent to $ids[] = 'mymodule';.
  $ids[] = basename(__FILE__, '.module');
}
