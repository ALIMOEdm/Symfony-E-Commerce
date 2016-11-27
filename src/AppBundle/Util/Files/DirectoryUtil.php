<?php
namespace AppBundle\Util\Files;

use Symfony\Component\Config\Definition\Exception\Exception;

class DirectoryUtil {

    protected static function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.self::getUploadDir();
    }

    protected static function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/images';
    }

    public static function createCatalogInUploadDir($dir)
    {
        $directory = self::getUploadRootDir().'/'.$dir;
        if ($handle = @opendir($directory)) {
            return true;
        }
        if (!mkdir($directory, 0777, true)) {
            throw new Exception('Не удалось создать каталог для сохранения изображений');
        }
        return true;
    }

    public function createCatalogTree(){

    }
}