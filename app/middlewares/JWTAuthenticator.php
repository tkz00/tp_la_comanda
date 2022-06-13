<?php

    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    class JWTAuthenticator
    {
        private static $secretKey = 'f2s0l0c0';
        private static $encryptionAlgorithm = 'HS256';

        public static function CreateToken($data)
        {
            $time = time();
            $payload = array(
                'iat' => $time,
                'exp' => $time + (60 * 60 * 24),
                'aud' => self::Aud(),
                'data' => $data,
                'app' => 'La Comanda'
            );

            return JWT::encode($payload, self::$secretKey, self::$encryptionAlgorithm);
        }

        public static function VerifyToken($token)
        {
            if(empty($token))
            {
                throw new Exception("Empty token.");
            }

            try
            {
                $result = JWT::decode(
                    $token,
                    new Key(self::$secretKey, self::$encryptionAlgorithm)
                );
            }
            catch(Exception $e)
            {
                throw $e;
            }

            if($result->aud !== self::Aud())
            {
                throw new Exception("User not valid.");
            }
        }

        public static function GetData($token)
        {
            return JWT::decode(
                $token,
                self::$secretKey,
                self::$encryptionAlgorithm
            )->data;
        }

        private static function Aud()
        {
            $aud = '';
    
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $aud = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $aud = $_SERVER['REMOTE_ADDR'];
            }
    
            $aud .= @$_SERVER['HTTP_USER_AGENT'];
            $aud .= gethostname();
    
            return sha1($aud);
        }
    }

?>