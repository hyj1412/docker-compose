<?php

/**
 * @file
 */

/**
 *
 */
class HTMLPurifier_HTMLModule_CommonAttributes extends HTMLPurifier_HTMLModule {
  /**
   * @type string
   */
  public $name = 'CommonAttributes';

  /**
   * @type array
   */
  public $attr_collections = array(
    'Core' => array(
      0 => array('Style'),
            // 'xml:space' => false,.
      'class' => 'Class',
      'id' => 'ID',
      'title' => 'CDATA',
    ),
    'Lang' => [],
    'I18N' => array(
  // proprietary, for xml:lang/lang.
      0 => array('Lang'),
    ),
    'Common' => array(
      0 => array('Core', 'I18N'),
    ),
  );

}

// vim: et sw=4 sts=4.