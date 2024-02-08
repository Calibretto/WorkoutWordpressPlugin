<?php
if ( class_exists( 'BHWorkoutPlugin_Common' ) == FALSE ) {
    class BHWorkoutPlugin_Common {
        public static function htmlSelect(array $options, string $id, ?string $selected, bool $required) : string {
            $html = "<select id='$id' name='$id'>";
            if ($required == FALSE) {
                $html .= "<option value='empty'>-</option>";
            }

            foreach($options as $option) {
                if ($option->value == $selected) {
                    $html .= "<option value='$option->value' selected>$option->value</option>";
                } else {
                    $html .= "<option value='$option->value'>$option->value</option>";
                }
            }

            $html .= "</select>";
            return $html;
        }
    }
}