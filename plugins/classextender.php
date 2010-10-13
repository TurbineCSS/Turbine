<?php


    /**
     * Class-Extender for container
     * 
     * Usage: Too complicated, see docs
     * Example: 
     * Status: Alpha
     * Version: 0.95
     * 
     * @param mixed &$parsed
     * @return void
     */
    function classextender(&$parsed){
        global $cssp;
        // Main loop
        foreach($parsed as $block => $css) {
            foreach($parsed[$block] as $selector => $styles) {
                $tokenized = $cssp->tokenize($selector, array('"',"'",','));
                
                // Define new and extended selector
                $extended_selector = null;
                $new_selector = $selector;
                
                foreach ($tokenized as $key => $token) {
                    // Looking for &.class, &#id or &:selector
                    if (preg_match_all('@(\&.|&#|&\:)@',$token,$matches)) {
                        // Remove the &
                        $new_selector = preg_replace('@( \&)@','',$selector);
                        $extended_selector = '';
                    // Looking for auto generated selector i.e. "a(:link, :visited)"
                    } elseif (preg_match_all('@(.*?)\((\:.*?)\)($| .*?$)@',$token,$matches)) {
                        $exploded_selectors = explode(',',$matches[2][0]);
                        foreach ($exploded_selectors as $key => $value) {
                            $extended_selector .= preg_replace('@\((\:.*?)\)@',trim($value),$token).", ";
                        }
                        $extended_selector = preg_replace('@(, )$@', '', $extended_selector);
                        $new_selector = str_replace($matches[0][0],  $extended_selector, $new_selector);

                        echo "Replace---->".$matches[0][0]."\n With---->".$extended_selector."\n Result---->".$new_selector."\n\n\n\n\n\n\n\n\n\n\n\n\n";
                        $extended_selector = '';
                    // Looking for auto generated selector i.e. "div.foo(1-3)"
                    } elseif (preg_match_all('@(.*?)\((\d{1,})-(\d{1,})\)@',$token,$matches)) {
                        // Check if starting value is smaller than ending value - "div.foo(3-1)" i.e. will be ignored
                        if ($matches[2][0] < $matches[3][0]) {
                            for ($i=$matches[2][0]; $i <= $matches[3][0]; $i++) {
                                $extended_selector .= preg_replace('@\((\d{1,})-(\d{1,})\)@',$i,$token).", ";
                            }
                            $extended_selector = preg_replace('@(, )$@', '', $extended_selector);
                            $new_selector = str_replace($matches[0][0],  $extended_selector, $new_selector);
                            $extended_selector = '';
                        }
                    }
                }
                // Insert the new selector if present
                if (isset($extended_selector)) {
                    // Remove ', ' at the end of the new selector if present
                    $changed = array();
                    $changed[$new_selector] = $styles;
                    $cssp->insert($changed,$block,$selector);
                    // Remove old selector
                    unset($parsed[$block][$selector]);
                }
            }
        }
    }


    /**
     * Register the plugin
     */
    $cssp->register_plugin('before_compile', 0, 'classextender');


?>