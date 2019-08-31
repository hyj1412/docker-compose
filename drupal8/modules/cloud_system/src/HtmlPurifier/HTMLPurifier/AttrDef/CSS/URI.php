<?php

/**
 * @file
 * Validates a URI in CSS syntax, which uses url('http://example.com')
 *
 * @note While theoretically speaking a URI in a CSS document could
 *       be non-embedded, as of CSS2 there is no such usage so we're
 *       generalizing it. This may need to be changed in the future.
 *
 * @warning Since HTMLPurifier_AttrDef_CSS blindly uses semicolons as
 *          the separator, you cannot put a literal semicolon in
 *          in the URI. Try percent encoding it, in that case.
 */

/**
 *
 */
class HTMLPurifier_AttrDef_CSS_URI extends HTMLPurifier_AttrDef_URI {

  /**
   *
   */
  public function __construct() {

    // Always embedded.
    parent::__construct(TRUE);
  }

  /**
   * @param string $uri_string
   * @param HTMLPurifier_Config $config
   * @param HTMLPurifier_Context $context
   * @return bool|string
   */
  public function validate($uri_string, $config, $context) {

    // Parse the URI out of the string and then pass it onto
    // the parent object.
    $uri_string = $this->parseCDATA($uri_string);
    if (strpos($uri_string, 'url(') !== 0) {
      return FALSE;
    }
    $uri_string = substr($uri_string, 4);
    if (strlen($uri_string) == 0) {
      return FALSE;
    }
    $new_length = strlen($uri_string) - 1;
    if ($uri_string[$new_length] != ')') {
      return FALSE;
    }
    $uri = trim(substr($uri_string, 0, $new_length));

    if (!empty($uri) && ($uri[0] == "'" || $uri[0] == '"')) {
      $quote = $uri[0];
      $new_length = strlen($uri) - 1;
      if ($uri[$new_length] !== $quote) {
        return FALSE;
      }
      $uri = substr($uri, 1, $new_length - 1);
    }

    $uri = $this->expandCSSEscape($uri);

    $result = parent::validate($uri, $config, $context);

    if ($result === FALSE) {
      return FALSE;
    }

    // Extra sanity check; should have been done by URI.
    $result = str_replace(array('"', "\\", "\n", "\x0c", "\r"), "", $result);

    // Suspicious characters are ()'; we're going to percent encode
    // them for safety.
    $result = str_replace(array('(', ')', "'"), array('%28', '%29', '%27'), $result);

    // there's an extra bug where ampersands lose their escaping on
    // an innerHTML cycle, so a very unlucky query parameter could
    // then change the meaning of the URL.  Unfortunately, there's
    // not much we can do about that...
    return "url(\"$result\")";
  }

}

// vim: et sw=4 sts=4.