<?php

    function JSON ($array) {
        /*  JSONifier (CC 3.0, MIT) 2011 Brian Lai
            
            JSON ()->array_to_json(mixed) => string [unicode]
            
            Converts a PHP array (associative or otherwise) to its 
            JSON string notation.
            
            If the input is not an array, it will be wrapped
            with one.
            
            Example:
            echo JSON (
                array (
                    "Keywords" => array (
                        "wax" => array (
                            "ID" => 1,
                            "Match" => 8
                        ),
                        "hard" => array (
                            "ID" => 1,
                            "Match" => 2
                        )
                    )
                )
            );
        */
        
        $a = new JSONifier ();
        if (!is_array ($array)) {
            $array = array ($array);
        }
        return $a->array_to_json ($array);
        
        } class JSONifier {
        
        function array_to_json ($array = array ()) {
            // uses assoc or not, depending.
            if ($this->is_assoc ($array)) {
                return $this->assoc_array_to_json ($array);
            } else {
                return $this->indexed_array_to_json ($array);
            }
        }
        
        private function assoc_array_to_json ($array) {
            // http://json.org/object.gif
            $buffer = '{';
            if (sizeof ($array) > 0) {
                foreach ($array as $key => $value) {
                    $buffer .= $this->escape_json_value ($key) . ":" . 
                               $this->escape_json_value ($value) . ",";
                }
                $buffer = substr ($buffer, 0, strlen ($buffer) - 1);
            }
            return "$buffer}"; // remove last comma;
        }
        
        private function indexed_array_to_json ($array) {
            // http://json.org/array.gif
            $buffer = '[';
            if (sizeof ($array) > 0) {
                foreach ($array as $value) {
                    $buffer .= $this->escape_json_value ($value) . ",";
                }
                $buffer = substr ($buffer, 0, strlen ($buffer) - 1);
            }
            return "$buffer]"; // remove last comma
        }
        
        private function escape_json_value ($val) {
            // http://json.org/value.gif
            // also escapes keys.
            if (is_null ($val)) {
                return 'null';
            } elseif (is_bool ($val)) {
                return $val ? 'true' : 'false';
            } elseif (is_numeric ($val)) {
                return sprintf ('%g', floatval ($val));
            } elseif (is_string ($val)) {
                return '"' . $this->escape_json_string ($val) . '"';
            } elseif (is_array ($val)) {
                return $this->array_to_json ($val);
            } 
        }
        
        private function escape_json_string ($val) {
            // http://json.org/string.gif
            $val = mb_convert_encoding ($val, "UTF-8");
            $len = mb_strlen ($val);
            $buffer = '';
            for ($i = 0; $i < $len; $i++) {
                switch ($val[$i]) {
                    case '"':
                    case "\\":
                    case '/':
                        $buffer .= "\\" . $val[$i];
                        break;
                    case "\b":
                        $buffer .= "\\b";
                        break;
                    case "\f":
                        $buffer .= "\\f";
                        break;
                    case "\n":
                        $buffer .= "\\n";
                        break;
                    case "\r":
                        $buffer .= "\\r";
                        break;
                    case "\t":
                        $buffer .= "\\t";
                        break;
                    default:
                        $buffer .= $val[$i];
                        break;
                }
            }
            return $buffer;
        }
        
        private function is_assoc ($array) {
            // even ONE associative key will cause the entire array
            // to be associative.
            $array_keys = array_keys ($array);
            $string_keys = array_filter ($array_keys, 'is_string');
            return (is_array ($array) && sizeof ($string_keys) > 0);
        }
    }
?>
