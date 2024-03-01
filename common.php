<?php
if ( class_exists( 'BHWorkoutPlugin_Common' ) == FALSE ) {
    class BHWorkoutPlugin_Common {
        public static function htmlSelect(array $options, string $id, ?string $selected, bool $multiple = FALSE) : string {
            if ($multiple) $id .= "[]";
            $html = "<select id='$id' name='$id'";
            if ($multiple) $html .= " multiple";
            $html .= ">";

            foreach($options as $value => $option) {
                if ($value == $selected) {
                    $html .= "<option value='$value' selected>$option</option>";
                } else {
                    $html .= "<option value='$value'>$option</option>";
                }
            }

            $html .= "</select>";
            return $html;
        }
    }
}