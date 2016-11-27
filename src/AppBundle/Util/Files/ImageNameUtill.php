<?php
namespace AppBundle\Util\Files;

use AppBundle\Util\Files\DirectoryUtil;
/**
 * Description of ImageNameUtill
 *
 * @author alimoedm
 */
class ImageNameUtill {
    //put your code here
    
    public static function getImageName($absolute_path, $directory){
        $image_numeric = 1;
        if ($handle = @opendir($absolute_path)) {
            $nums = array();
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $ext_2 = explode('.', $entry);
                    $name_parts = explode('_', $ext_2[0]);
                    $num = $name_parts[1];
                    $nums[] = $num;
                }
            }
            while(true){
                if( in_array($image_numeric, $nums)){
                    $image_numeric++;
                    continue;
                }
                break;
            }
            closedir($handle);
        }else{
            DirectoryUtil::createCatalogInUploadDir($directory);
        }
        return $image_numeric;
    }
}
