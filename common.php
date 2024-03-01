<?php
if ( class_exists( 'BHWorkoutPlugin_Common' ) == FALSE ) {
    class BHWorkoutPlugin_Common {
        public static function htmlSelect(
            array $options, 
            string $id, 
            string|array|null $selected, 
            bool $multiple = FALSE
        ) : string {
            if ($multiple) $id .= "[]";
            $html = "<select id='$id' name='$id'";
            if ($multiple) $html .= " multiple";
            $html .= ">";

            foreach($options as $value => $option) {
                if (self::is_selected($value, $selected)) {
                    $html .= "<option value='$value' selected>$option</option>";
                } else {
                    $html .= "<option value='$value'>$option</option>";
                }
            }

            $html .= "</select>";
            return $html;
        }

        private static function is_selected(
            string $id, 
            string|array|null $selected
        ) : bool {
            if (is_null($selected)) {
                return FALSE;
            } else if (is_array($selected)) {
                return in_array($id, $selected);
            } else {
                return $id === $selected;
            }
        }
    }
}