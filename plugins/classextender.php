<?php


  /**
   * Simple Class-Extender for container
   * 
   * Usage:   
   * Example: 
   * Status:  Alpha
   * Version: 0.1
   * 
   * @param mixed &$parsed
   * @return void
   */
  function classExtender(&$parsed){
    global $cssp;
    // Main loop
    foreach($parsed as $block => $css){
      foreach($parsed[$block] as $selector => $styles){
        if (preg_match('@(\&.|&#|&\:)@',$selector)) {
          $extended_selector = preg_replace('@( \&)@','',$selector);
          $changed = array();
          $changed[$extended_selector] = $styles;
          $cssp->insert($changed,'global',$selector);
          unset($parsed[$block][$selector]);
        } elseif (preg_match('@(.*?)\((\:.*?)\)@',$selector,$matches)) {
          $exploded_selectors = explode(',',$matches[2]);
          
          foreach ($exploded_selectors as $key => $value) {
            $extended_selector = $matches[1].trim($value);
            $changed = array();
            $changed[$extended_selector] = $styles;
            $cssp->insert($changed,'global',$selector);
          }
          unset($parsed[$block][$selector]);
        }
      }
    }
  }


  /**
   * Register the plugin
   */
  $cssp->register_plugin('before_compile', 0, 'classExtender');


?>