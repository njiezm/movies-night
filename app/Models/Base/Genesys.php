<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genesys extends Model
{
    use HasFactory;

    static function Crypt($chaine){
        $ciphering = "AES-256-OFB"; 
        $iv_length = openssl_cipher_iv_length($ciphering); 
        $options = 0; 
        $encryption_iv = '5424358513959785'; 
        $encryption_key = env('5424358513959785'); 
        $code = openssl_encrypt($chaine, $ciphering, 
                    $encryption_key, $options, $encryption_iv); 
        return $code;
    }

    static function Decrypt($chaine){
        $ciphering = "AES-256-OFB"; 
        $decryption_iv = env('5424358513959785'); 
        $decryption_key = env('5424358513959785'); 
        $options = 0; 
        $code=openssl_decrypt ($chaine, $ciphering,  
        $decryption_key, $options, $decryption_iv); 
      
        return $code;
    }

    static function GenCodeAlpha($number){
        $chaine ="AZERTYUIPQSDFGHJKLMWXCVBN";
            srand((double)microtime()*1000000);
            $pass = '';
            for($i=0; $i<$number; $i++){
                $pass .= $chaine[rand()%strlen($chaine)];
            }
        $code  = $pass;
        return $code;
    }

    static function GenCodeAlphaNum($number){
        $chaine ="AZERTYUIOPQSDFGHJKLMWXCVBN0123456789azertyuiopqsdfghjklmwxcvbn";
            srand((double)microtime()*1000000);
            $pass = '';
            for($i=0; $i<$number; $i++){
                $pass .= $chaine[rand()%strlen($chaine)];
            }
        $code  = $pass;
        return $code;
    }

    static function GenCodeNum($number){
        $chaine ="0123456789";
            srand((double)microtime()*1000000);
            $pass = '';
            for($i=0; $i<$number; $i++){
                $pass .= $chaine[rand()%strlen($chaine)];
            }
        $code  = $pass;
        return $code;
    }

    static function GenSlugCode($text){
        $search  = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ',' ','-','_',"'",'"','&','$','%','*');
        $replace = array('A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', '','','','','','','','','');

        $code  =str_replace($search, $replace, $text);
        return $code;
    }


    static function ScanIp(){
        
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return $ip;
    }
}
