<?php

/**
 * @file
 * Validates Color as defined by CSS.
 */

/**
 *
 */
class HTMLPurifier_AttrDef_CSS_Color extends HTMLPurifier_AttrDef {

  /**
   * @param string $color
   * @param HTMLPurifier_Config $config
   * @param HTMLPurifier_Context $context
   * @return bool|string
   */
  public function validate($color, $config, $context) {

    static $colors = NULL;
    if ($colors === NULL) {
      $colors = $config->get('Core.ColorKeywords');
    }

    $color = trim($color);
    if ($color === '') {
      return FALSE;
    }

    $lower = strtolower($color);
    if (isset($colors[$lower])) {
      return $colors[$lower];
    }

    if (strpos($color, 'rgb(') !== FALSE) {
      // Rgb literal handling.
      $length = strlen($color);
      if (strpos($color, ')') !== $length - 1) {
        return FALSE;
      }
      $triad = substr($color, 4, $length - 4 - 1);
      $parts = explode(',', $triad);
      if (count($parts) !== 3) {
        return FALSE;
      }
      // To ensure that they're all the same type.
      $type = FALSE;
      $new_parts = [];
      foreach ($parts as $part) {
        $part = trim($part);
        if ($part === '') {
          return FALSE;
        }
        $length = strlen($part);
        if ($part[$length - 1] === '%') {
          // Handle percents.
          if (!$type) {
            $type = 'percentage';
          }
          elseif ($type !== 'percentage') {
            return FALSE;
          }
          $num = (float) substr($part, 0, $length - 1);
          if ($num < 0) {
            $num = 0;
          }
          if ($num > 100) {
            $num = 100;
          }
          $new_parts[] = "$num%";
        }
        else {
          // Handle integers.
          if (!$type) {
            $type = 'integer';
          }
          elseif ($type !== 'integer') {
            return FALSE;
          }
          $num = (int) $part;
          if ($num < 0) {
            $num = 0;
          }
          if ($num > 255) {
            $num = 255;
          }
          $new_parts[] = (string) $num;
        }
      }
      $new_triad = implode(',', $new_parts);
      $color = "rgb($new_triad)";
    }
    else {
      // Hexadecimal handling.
      if ($color[0] === '#') {
        $hex = substr($color, 1);
      }
      else {
        $hex = $color;
        $color = '#' . $color;
      }
      $length = strlen($hex);
      if ($length !== 3 && $length !== 6) {
        return FALSE;
      }
      if (!ctype_xdigit($hex)) {
        return FALSE;
      }
    }
    return $color;
  }

}

// vim: et sw=4 sts=4.