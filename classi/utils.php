<?php
    class utils
    {
        private $connessione;

        public function __construct($connessione)
        {
            $this->connessione = $connessione;
        }

        public function query($query, $params, $return_assoc = true, $procedurale = false)
        {
            if ($stmt = $this->connessione->prepare($query)) {
                call_user_func_array(array($stmt, 'bind_param'), self::refValues($params));

                if ($stmt->execute()) {
                    if ($return_assoc) {
                        if ($procedurale) {
                            return self::estrazioneDatiProcedure($stmt);
                        } else {
                            $result = $stmt->get_result();
                            $outp = $result->fetch_all(MYSQLI_ASSOC);
                            return $outp;
                        }
                    } else {
                        return true;
                    }
                }
            }
            return false;
        }

        private function estrazioneDatiProcedure($stmt)
        {
            $i = 0;
            do {
                $res = $stmt->get_result();
                if ($res) {
                    $row = $res->fetch_all(MYSQLI_ASSOC);

                    if ($row != null) {
                        $outp[$i++] = $row;
                    }
                             
                    $res->free();
                }
            } while ($stmt->more_results() && $stmt->next_result());

            return $outp;
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
