<?php
class Solution {

    /**
     * @param String $s
     * @return Integer
     */
    function lengthOfLongestSubstring($s) {
        $max = 0;
        $current = '';
        $len=$i=0;
        for (;$i < strlen($s); $i++) {
            if (strpos($current, $s[$i]) !== false) {
                echo $current;
                $current = substr($current, strpos($current, $s[$i])+1);
                $len = strlen($current);

            }

            $current .= $s[$i];
            $len++;
            $max = max($max, $len);
        }

        return $max;
    }
}

$obj = new Solution();
echo $obj->lengthOfLongestSubstring('pwwkew');