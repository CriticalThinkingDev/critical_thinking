<?php

class Potato_Compressor_Model_Compressor_Image
{
    const JPEG_WIN_FILENAME = 'jpegoptim.exe';
    const JPEG_UNIX_FILENAME = 'jpegoptim';

    const GIF_WIN_FILENAME = 'gifsicle.exe';
    const GIF_UNIX_FILENAME = 'gifsicle';

    const PNG_WIN_FILENAME = 'optipng.exe';
    const PNG_UNIX_FILENAME = 'optipng';

    const MEDIA_ORIGINAL_FOLDER_NAME = 'media_original_images';
    const SKIN_ORIGINAL_FOLDER_NAME = 'skin_original_images';

    const TRANSPARENT_IMG_BASE64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGP6zwAAAgcBApocMXEAAAAASUVORK5CYII=';

    public function optimizeImage($image)
    {
        if (Mage::helper('po_compressor')->isIgnoredImage($image) ||
            Mage::helper('po_compressor')->isOptimizedImage($image)
        ) {
            return true;
        }
        if (Mage::helper('po_compressor/config')->isAllowImageBackup()) {
            $this->_backupImage($image);
        }
        $applicationPath = BP . DS . 'lib' . DS . 'Compressor' . DS . 'img_optimization_tools' . DS  . 'unix' . DS . '64';
        if ($this->_getIsWinOs()) {
            $applicationPath = BP . DS . 'lib' . DS . 'Compressor' . DS . 'img_optimization_tools' . DS  . 'win32';
        } else if(!$this->_is64bit()) {
            $applicationPath = BP . DS . 'lib' . DS . 'Compressor' . DS . 'img_optimization_tools' . DS  . 'unix';
        }

        $info = getimagesize($image);
        $_result = array();
        switch($info[2]) {
            case 1:
            case 3:
                if ($info[2] == 3 || $info[2] == 1 && !$this->_isAnimatedGif($image)) {
                    //PNG or GIF without animate
                    $libPath = $applicationPath . DS . self::PNG_UNIX_FILENAME;
                    if ($this->_getIsWinOs()) {
                        $libPath = $applicationPath . DS . self::PNG_WIN_FILENAME;
                    }
                    if (!Potato_Compressor_Helper_Config::canUseDefaultPng()) {
                        $libPath = Potato_Compressor_Helper_Config::pngPath();
                    }
                    $pngFileName = dirname($image) . DS . basename($image, ".gif") . '.png';
                    if ($info[2] == 1 && file_exists($pngFileName)) {
                        //after optimization img may be renamed to .png -> need do backup if same file already exists
                        rename($pngFileName, $pngFileName . '_tmp');
                    }

                    exec(
                        $libPath . ' ' . Potato_Compressor_Helper_Config::pngOptions() . ' ' . $image . ' 2>&1',
                        $_result,
                        $_error
                    );
                    if (empty($_result) || $_error != 0) {
                        throw new Exception(
                            Mage::helper('po_compressor')->__(
                                'Application for Optimization PNG files return error code %s %s', $_error, implode(' ', $_result)
                            )
                        );
                    }
                    if ($info[2] == 1 && file_exists($pngFileName)) {
                        rename($pngFileName, $image);
                    }
                    if ($info[2] == 1 && file_exists($pngFileName . '_tmp')) {
                        //restore previously renamed image
                        rename($pngFileName . '_tmp', $pngFileName);
                    }
                    break;
                }
                //GIF with animate
                $libPath = $applicationPath . DS . self::GIF_UNIX_FILENAME;
                if ($this->_getIsWinOs()) {
                    $libPath = $applicationPath . DS . self::GIF_WIN_FILENAME;
                }
                if (!Potato_Compressor_Helper_Config::canUseDefaultGif()) {
                    $libPath = Potato_Compressor_Helper_Config::gifPath();
                }
                exec(
                    $libPath . ' ' . Potato_Compressor_Helper_Config::gifOptions() . ' ' . $image . ' 2>&1',
                    $_result,
                    $_error
                );
                if (empty($_result) || $_error != 0) {
                    throw new Exception(
                        Mage::helper('po_compressor')->__(
                            'Application for Optimization GIF files return error code %s %s', $_error, implode(' ', $_result)
                        )
                    );
                }
                break;
            case 2:
                //JPG
                $libPath = $applicationPath . DS . self::JPEG_UNIX_FILENAME;
                if ($this->_getIsWinOs()) {
                    $libPath = $applicationPath . DS . self::JPEG_WIN_FILENAME;
                }
                if (!Potato_Compressor_Helper_Config::canUseDefaultJpg()) {
                    $libPath = Potato_Compressor_Helper_Config::jpgPath();
                }
                exec(
                    $libPath . ' ' . Potato_Compressor_Helper_Config::jpgOptions() . ' ' . $image . ' 2>&1',
                    $_result,
                    $_error
                );
                if (empty($_result) || $_error != 0) {
                    throw new Exception(
                        Mage::helper('po_compressor')->__(
                            'Application for Optimization JP(E)G files return error code %s %s', $_error, implode(' ', $_result)
                        )
                    );
                }
                break;
        }
        $optimizedImage = Mage::getModel('po_compressor/image');
        $optimizedImage
            ->setHash(Mage::helper('po_compressor')->getImageHash($image))
            ->save()
        ;
        Mage::helper('po_compressor')->removeImageGalleryCache();
        Mage::log(print_r($_result,true),1,'po_cmp.log', true);
        return true;
    }

    public function replaceImageUrl($response)
    {
        $body = $response->getBody();
        //get all img
        preg_match_all('/<img.*src=("[^"]+").*>/', $body, $matches);
        if (empty($matches)) {
            return $body;
        }
        foreach ($matches[0] as $imageTag) {
            //check ignore
            preg_match('@po_cmp_ignore@', $imageTag, $match);
            if (!empty($match)) {
                continue;
            }
            //get image url
            if (!$imageUrl = $this->_parseImageUrl($imageTag)) {
                continue;
            }
            //check image is media or skin
            if (!Potato_Compressor_Helper_Data::isMediaImage($imageUrl) &&
                !Potato_Compressor_Helper_Data::isSkinImage($imageUrl)
            ) {
                continue;
            }
            //set dimension function
            $newImageTag = str_replace('<img', '<img onload="setImageDimension(this)"', $imageTag);
            if (Potato_Compressor_Helper_Data::canScaleImage($imageUrl)) {
                //set scale function
                //remove src attribute
                $newImageTag = str_replace($imageUrl, self::TRANSPARENT_IMG_BASE64, $imageTag);
                if (Potato_Compressor_Helper_Data::isSkinImage($imageUrl)) {
                    $type = 'skin';
                    $imageUrl = 'skin/' . str_replace(Mage::getBaseUrl('skin', Mage::app()->getRequest()->isSecure()), '', $imageUrl);
                } else {
                    $type = 'media';
                    $imageUrl = 'media/' . str_replace(Mage::getBaseUrl('media', Mage::app()->getRequest()->isSecure()), '', $imageUrl);
                }
                $newImageTag = str_replace('<img', '<img onload="serveScaledImage(this, \'' . $imageUrl . '\', \'' . $type . '\')"', $newImageTag);
            }
            $body = str_replace($imageTag, $newImageTag, $body);
        }
        $response->setBody($body);
        return $this;
    }

    protected function _parseImageUrl($imageTag)
    {
        preg_match_all('/src="([^"]+)"/', $imageTag, $matches);
        if (empty($matches)) {
            return false;
        }
        return $matches[1][0];
    }

    protected function _is64bit() {
        $int = "9223372036854775807";
        $int = intval($int);
        if ($int == 9223372036854775807) {
            /* 64bit */
            return true;
        }
        return false;
    }

    protected function _backupImage($image)
    {
        $path = str_replace(BP . DS, '', Mage::helper('po_compressor')->getBackupImagePath($image));
        if (isset($path)) {
            $rootPath = BP;
            foreach (explode(DS, $path) as $target) {
                $rootPath .= DS . $target;
                if (file_exists($rootPath)) {
                    continue;
                }
                $info = pathinfo($rootPath);
                if (array_key_exists('extension', $info) && $info['extension'] != '') {
                    $content = file_get_contents($image);
                    file_put_contents($rootPath, $content);
                    break;
                }
                Potato_Compressor_Helper_Data::prepareFolder($rootPath);
            }
        }
        return $this;
    }

    protected function _getIsWinOs()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            return false;
        }
        return true;
    }

    protected function _isAnimatedGif($image)
    {
        $content = file_get_contents($image);
        $strLoc = 0;
        $count = 0;

        // There is no point in continuing after we find a 2nd frame
        while ($count < 2)
        {
            $where1 = strpos($content, "\x00\x21\xF9\x04", $strLoc);
            if ($where1 === FALSE) {
                break;
            }
            $str_loc = $where1+1;
            $where2  = strpos($content, "\x00\x2C", $str_loc);
            if ($where2 === FALSE) {
                break;
            } else {
                if ($where1 + 8 == $where2) {
                    $count++;
                }
                $strLoc = $where2 + 1;
            }
        }
        // gif is animated when it has two or more frames
        return ($count >= 2);
    }
}