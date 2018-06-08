<?php
    class utils
    {
        private $connessione;

        public function __construct($connessione)
        {
            $this->connessione = $connessione;
        }

        public function query($query, $params, $return_assoc = true)
        {
            if ($stmt = $this->connessione->prepare($query)) {
                call_user_func_array(array($stmt, 'bind_param'), self::refValues($params));

                if ($stmt->execute()) {
                    if ($return_assoc) {
                        $result = $stmt->get_result();
                        $outp = $result->fetch_all(MYSQLI_ASSOC);
                        return $outp;
                    }
                }
            }
        }

        public function refValues($arr)
        {
            if (strnatcmp(phpversion(), '5.3') >= 0) { //Reference is required for PHP 5.3+
                $refs = array();
                $type = '';

                foreach ($arr as $key => $value) {
                    $type .= (is_string($value)) ? 's' : 'i';
                    $refs[] = &$arr[$key];
                }

                return  array_merge(array($type), $refs);
            }
        }
    }
