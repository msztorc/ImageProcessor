<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <https://github.com/msztorc/ImageProcessor>.
 */

/**
 * ImageProcessor is a wrapper for graphics libraries like GD2, ImageMagick and epeg.
 * Implements often used methods of graphics manipulation.
 *
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Mirosław Sztorc <miroslaw@sztorc.com>
 */

namespace ImageProcessor;

class ImageProcessor
{
    /**
         * Image resource.
         *
         * @var null
         */
        private $image = null;

        /**
         * Image format supported by the version of GD.
         *
         * @var null
         */
        private $imageType = null;

        /**
         * Image processing library (supported GD2 or Imagick).
         *
         * @var string
         */
        private $libType = 'gd';

        /**
         * Image filename.
         *
         * @var null
         */
        private $imageFile = null;

        /**
         * Image width.
         *
         * @var int
         */
        private $imageWidth = 0;

        /**
         * Image height.
         *
         * @var int
         */
        private $imageHeight = 0;

        /**
         * Image extension.
         *
         * @var null
         */
        private $imageExtension = null;

        /**
         * Imagick filter constant.
         *
         * @var [type]
         */
        private $imageFilter = \Imagick::FILTER_LANCZOS;

        /**
         * Constructor.
         *
         * @param string $lib       Image processing library (gd or imagick)
         * @param string $imageFile Image filename
         */
        public function __construct($lib = 'gd', $imageFile = null)
        {
            $this->libType = $lib;

            if ($imageFile != null && file_exists($imageFile)) {
                $this->open($imageFile);
            } elseif ($imageFile != null) {
                throw new \Exception("File doesn't exists");
            }

            if ($imageFile == null && $lib == 'imagick') {
                $this->image = new \Imagick();
            }
        }

        /**
         * Opens the image.
         *
         * @param string $imageFile filename
         *
         * @return object
         */
        public function open($imageFile)
        {
            if (!file_exists($imageFile)) {
                throw new \Exception("File doesn't exists");
            }

            $this->imageFile = $imageFile;

            list($this->imageWidth, $this->imageHeight, $this->imageType) = @getimagesize($this->imageFile);
            if ($this->imageWidth == 0 || $this->imageHeight == 0) {
                throw new \Exception('Error image size');
            }

            switch ($this->libType) {

                case 'gd':
                    switch ($this->imageType) {
                        case IMAGETYPE_JPEG: $this->image = imagecreatefromjpeg($this->imageFile); $this->imageExtension = 'jpg'; break;
                        case IMAGETYPE_PNG:  $this->image = imagecreatefrompng($this->imageFile);  $this->imageExtension = 'png'; break;
                        case IMAGETYPE_GIF:  $this->image = imagecreatefromgif($this->imageFile);  $this->imageExtension = 'gif'; break;

                        default:
                            throw new \Exception('Unsupported image format (only jpg/png/gif)');
                    }

                break;
                case 'imagick':

                    if ($this->image == null) {
                        $this->image = new \Imagick();
                    }

                    $this->image->readImage($this->imageFile);

                break;
            }

            return $this;
        }

        /**
         * Clears the image.
         *
         * @return bool
         */
        public function clear()
        {
            if ($this->image != null) {
                switch ($this->libType) {

                    case 'gd':
                        imagedestroy($this->image);
                    break;
                    case 'imagick':
                        $this->image->clear();
                        //$this->image->destroy();
                    break;
                }
            }

            $this->imageFile = null;
            $this->imageType = null;
            $this->imageExtension = null;
            $this->libType = 'gd';
            $this->imageWidth = 0;
            $this->imageHeight = 0;

            $this->image = null;

            return true;
        }

        /**
         * Gets the image resource.
         *
         * @return image
         */
        public function image()
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            return $this->image;
        }

        /**
         * Updates image width and height after processing.
         *
         * @return bool
         */
        private function _updateImageSize()
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            switch ($this->libType) {

                case 'gd':
                    $this->imageWidth = imagesx($this->image());
                    $this->imageHeight = imagesy($this->image());
                break;
                case 'imagick':
                    $this->imageWidth = $this->image()->getImageWidth();
                    $this->imageHeight = $this->image()->getImageHeight();
                break;
            }

            return true;
        }

        /**
         * Gets width of the image.
         *
         * @return int
         */
        public function width()
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            $width = 0;

            switch ($this->libType) {

                case 'gd':
                    $width = $this->imageWidth;
                break;
                case 'imagick':
                    $width = $this->image()->getImageWidth();
                break;
            }

            return $width;
        }

        /**
         * Gets height of the image.
         *
         * @return int
         */
        public function height()
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            $height = 0;

            switch ($this->libType) {

                case 'gd':
                    $height = $this->imageHeight;
                break;
                case 'imagick':
                    $height = $this->image()->getImageHeight();
                break;
            }

            return $height;
        }

        /**
         * Gets the type of the image.
         *
         * @return bit-field corresponding to the image formats
         */
        public function type()
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            return $this->imageType;
        }

        /**
         * Gets type of library used in processing.
         *
         * @return string
         */
        public function libType()
        {
            return $this->libType;
        }

        /**
         * Gets image extension.
         *
         * @return string
         */
        public function extension()
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            return $this->imageExtension;
        }

        /**
         * Gets image file.
         *
         * @return string
         */
        public function file()
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            return $this->imageFile;
        }

        /**
         * Returns copy of the image.
         *
         * @param int $width
         * @param int $height
         * @param int $left
         * @param int $top
         *
         * @return object
         */
        private function _gd_image_copy($width, $height, $left, $top)
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            $_img = imagecreatetruecolor($width, $height);

            if ($this->imageType == IMAGETYPE_PNG) {
                imagefill($_img, 0, 0, imagecolorallocatealpha($_img, 0, 0, 0, 127));
                imagealphablending($_img, false);
                imagesavealpha($_img, true);
            }

            if (!imagecopy($_img, $this->image, 0, 0, $left, $top, $width, $height)) {
                throw new \Exception('Error when copy image');
            }

            return $_img;
        }

        /**
         * Returns copy of the image.
         *
         * @param int $width
         * @param int $height
         * @param int $left
         * @param int $top
         *
         * @return object
         */
        public function copy()
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            $clone = null;

            switch ($this->libType) {
                case 'gd':
                    $clone = $this->_gd_image_copy($this->imageWidth, $this->imageHeight, 0, 0);
                break;
                case 'imagick':
                    $clone = $this->image->clone();
                break;
            }

            return $clone;
        }

        /**
         * Scales an image using GD library.
         *
         * @param int $width  new width of the image
         * @param int $height new height of the image
         *
         * @return object
         */
        private function _gd_image_resize($width, $height)
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            $_img = imagecreatetruecolor($width, $height);

            if ($this->imageType == IMAGETYPE_PNG) {
                imagealphablending($_img, false);
                imagesavealpha($_img, true);
            }

            if (!imagecopyresampled($_img, $this->image, 0, 0, 0, 0, $width, $height, $this->imageWidth, $this->imageHeight)) {
                throw new \Exception('Error when resize image');
            }

            $this->image = $_img;
            $this->imageWidth = $width;
            $this->imageHeight = $height;

            return $this;
        }

        /**
         * Scales an image.
         *
         * @param int  $width   new width of image
         * @param int  $height  new height of image
         * @param bool $aratio  aspect ratio
         * @param bool $enlarge allow for enlarge
         *
         * @return object
         */
        public function resize($width, $height, $aratio = true, $enlarge = false)
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            switch ($this->libType) {

                case 'gd':

                    $newWidth = 0; $newHeight = 0;

                    if ($enlarge || !$aratio) {
                        $newWidth = $width;
                        $newHeight = $height;
                    }

                    if ($width > 0 && $aratio && (($width < $this->imageWidth && !$enlarge) || $enlarge)) {
                        $newWidth = (int) $width;
                        $newHeight = round(($width * $this->imageHeight) / $this->imageWidth);

                        $this->_gd_image_resize($newWidth, $newHeight);
                    }

                    if ($height > 0 && $aratio && (($height < $this->imageHeight && !$enlarge) || $enlarge)) {
                        $newWidth = round(($this->imageWidth * $height) / $this->imageHeight);
                        $newHeight = (int) $height;

                        $this->_gd_image_resize($newWidth, $newHeight);
                    }

                    if (!$aratio && $width > 0 && $height > 0) {
                        $this->_gd_image_resize($newWidth, $newHeight);
                    }

                break;
                case 'imagick':
                    $this->image->resizeImage($width, $height, $this->imageFilter, 1, $aratio);
                break;

            }

            $this->_updateImageSize();

            return $this;
        }

        /**
         * Extracts a region of the image.
         *
         * @param int $width  The width of the crop
         * @param int $height The height of the crop
         * @param int $x      The X coordinate of the cropped region's top left corner
         * @param int $y      The Y coordinate of the cropped region's top left corner
         *
         * @return object
         */
        public function crop($width, $height, $x, $y)
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            switch ($this->libType) {
                case 'gd':
                    $img_cropped = $this->_gd_image_copy($width, $height, $x, $y);
                    $this->image = $img_cropped;
                break;
                case 'imagick':
                    if (!$this->image->cropImage($width, $height, $x, $y)) {
                        throw new \Exception('Error when cropping (imagick)');
                    }

                break;
            }

	    $this->_updateImageSize();

            return $this;
        }

        /**
         * Change the brightness.
         *
         * @param int $threshold
         *
         * @return object
         */
        public function brightness($threshold = 100)
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            switch ($this->libType) {
                case 'gd':
                    $threshold = round((int) $threshold);
                    $threshold = ($threshold < -255) ? -255 : (($threshold > 255) ? 255 : $threshold);

                    if (!imagefilter($this->image, IMG_FILTER_BRIGHTNESS, $threshold)) {
                        throw new \Exception('Error when brightness processing (gd2)');
                    }
                break;
                case 'imagick':
                    if (!$this->image->modulateImage($threshold, 100, 100)) {
                        throw new \Exception('Error when processing negate (imagick)');
                    }

                break;
            }

            return $this;
        }

        /**
         * Image inversion.
         *
         * @return object
         */
        public function negative()
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            switch ($this->libType) {
                case 'gd':

                    if (!imagefilter($this->image, IMG_FILTER_NEGATE)) {
                        throw new \Exception('Error when negative processing (gs2)');
                    }
                break;
                case 'imagick':

                    if (!$this->image->negateImage(false)) {
                        throw new \Exception('Error when processing negate (imagick)');
                    }

                break;
            }

            return $this;
        }

        /**
         * Change the contrast of the image.
         *
         * @param int $threshold
         *
         * @return object
         */
        public function contrast($threshold = 0)
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            switch ($this->libType) {

                case 'gd':
                    $threshold = round((int) $threshold);
                    $threshold = ($threshold < -100) ? -100 : (($threshold > 100) ? 100 : $threshold);

                    if (!imagefilter($this->image, IMG_FILTER_CONTRAST, $threshold)) {
                        throw new \Exception('Error when contrast processing (gd2)');
                    }
                break;
                case 'imagick':

                    if (!$this->image->contrastImage(1)) {
                        throw new \Exception('Error when processing contrast (imagick)');
                    }

                break;
            }

            return $this;
        }

        /**
         * Blends the fill color with the image.
         *
         * @param int $red   red color value
         * @param int $green green color value
         * @param int $blue  blue color value
         *
         * @return object
         */
        public function colorize($red, $green, $blue)
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            switch ($this->libType) {
                case 'gd':

                    $red = round((int) $red);
                    $green = round((int) $green);
                    $blue = round((int) $blue);

                    $red = ($red  < -255) ? -255 : (($red  > 255) ? 255 : $red);
                    $green = ($green  < -255) ? -255 : (($green  > 255) ? 255 : $green);
                    $blue = ($blue  < -255) ? -255 : (($blue  > 255) ? 255 : $blue);

                    if (!imagefilter($this->image, IMG_FILTER_COLORIZE, $red, $green, $blue)) {
                        throw new \Exception('Error when processing colorize (gd2)');
                    }
                break;
                case 'imagick':
                    $hex_color = sprintf('%02X%02X%02X', $red, $green, $blue);

                    if (!$this->image->colorizeImage('#'.$hex_color, 1.0)) {
                        throw new \Exception('Error when processing colorize (imagick)');
                    }

                break;
            }

            return $this;
        }

        /**
         * Applies grayscale tone.
         *
         * @return object
         */
        public function grayscale()
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            switch ($this->libType) {
                case 'gd':

                    if (!imagefilter($this->image, IMG_FILTER_GRAYSCALE)) {
                        throw new \Exception('Error when processing grayscale (gd2)');
                    }

                break;
                case 'imagick':
                    if (!$this->image->setImageColorSpace(\Imagick::COLORSPACE_GRAY)) {
                        throw new \Exception('Error when processing grayscale (imagick)');
                    }

                break;
            }

            return $this;
        }

        /**
         * Applies a sepia tone.
         *
         * @return object
         */
        public function sepia()
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            switch ($this->libType) {
                case 'gd':

                    $this->grayscale();
                    $this->colorize(90, 60, 40);

                break;
                case 'imagick':

                    if (!$this->image->sepiaToneImage(83)) {
                        throw new \Exception('Error when processing sepia (imagick)');
                    }

                break;
            }

            return $this;
        }

        /**
         * Creates a horizontal mirror image.
         *
         * @return object
         */
        public function mirror()
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            switch ($this->libType) {
                case 'gd':

                    $width = imagesx($this->image);
                    $height = imagesy($this->image);

                    $src_x = $width - 1;
                    $src_y = 0;
                    $src_width = -$width;
                    $src_height = $height;

                    $imgdest = imagecreatetruecolor($width, $height);

                    imagecopyresampled($imgdest, $this->image, 0, 0, $src_x, $src_y, $width, $height, $src_width, $src_height);
                    $this->image = $imgdest;

                break;
                case 'imagick':

                    $this->image->flopImage();

                break;
            }

            return $this;
        }

        /**
         * Creates a horizontal mirror image.
         *
         * @return object
         */
        public function flop()
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            switch ($this->libType) {
                case 'gd':

                    imageflip($this->image, IMG_FLIP_HORIZONTAL);

                break;
                case 'imagick':

                    $this->image->flopImage();

                break;
            }

            return $this;
        }

        /**
         * Creates a vertical mirror image.
         *
         * @return object
         */
        public function flip()
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            switch ($this->libType) {
                case 'gd':

                    imageflip($this->image, IMG_FLIP_VERTICAL);

                break;
                case 'imagick':

                    $this->image->flipImage();

                break;
            }

            return $this;
        }

        /**
         * Rotate an image with a given angle.
         *
         * @param int $angle Rotation angle, in degrees (may be negative)
         *
         * @return object
         */
        public function rotate($angle)
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            switch ($this->libType) {
                case 'gd':

                    $img_copy = $this->image;

                    $angle = ($angle > 0) ? (360 - $angle) : abs($angle);
                    $this->image = imagerotate($img_copy, $angle, 0);

                break;
                case 'imagick':

                    $this->image->rotateImage('#000', $angle);

                break;
            }

            $this->_updateImageSize();

            return $this;
        }

        /**
         * Auto-rotate image based on orientation from exif metadata.
         *
         * @return object
         */
        public function autorotate()
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            switch ($this->libType) {
                case 'gd':

                    $exif = (function_exists('exif_read_data')) ? exif_read_data($this->imageFile) : die('exif_read_data function not found');

                    if ($exif && isset($exif['Orientation'])) {
                        $orientation = $exif['Orientation'];

                        if ($orientation != 1) {
                            $mirror = false;
                            $deg = 0;

                            switch ($orientation) {
                              case 2:
                                $mirror = true;
                                break;
                              case 3:
                                $deg = 180;
                                break;
                              case 4:
                                $deg = 180;
                                $mirror = true;
                                break;
                              case 5:
                                $deg = 270;
                                $mirror = true;
                                break;
                              case 6:
                                $deg = 270;
                                break;
                              case 7:
                                $deg = 90;
                                $mirror = true;
                                break;
                              case 8:
                                $deg = 90;
                                break;
                            }

                            $img_copy = $this->image;

                            if ($deg) {
                                $this->image = imagerotate($img_copy, $deg, 0);
                            }
                            if ($mirror) {
                                $this->mirror();
                            }
                        }
                    }

                break;
                case 'imagick':

                    $orientation = $this->image->getImageOrientation();

                    switch ($orientation) {
                        case \Imagick::ORIENTATION_BOTTOMRIGHT:
                            $this->image->rotateimage('#000', 180); // rotate 180 degrees 
                        break;

                        case \Imagick::ORIENTATION_RIGHTTOP:
                            $this->image->rotateimage('#000', 90); // rotate 90 degrees CW 
                        break;

                        case \Imagick::ORIENTATION_LEFTBOTTOM:
                            $this->image->rotateimage('#000', -90); // rotate 90 degrees CCW 
                        break;
                    }

                    // Now that it's auto-rotated, make sure the EXIF data is correct in case the EXIF gets saved with the image! 
                    $this->image->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);

                break;
            }

            $this->_updateImageSize();

            return $this;
        }

        /**
         * Displays the image.
         *
         * @param int $quality Quality of compression (GD only)
         *
         * @return blob
         */
        public function display($quality = 100)
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            switch ($this->libType) {

                case 'gd':

                    switch ($this->imageType) {
                        case IMAGETYPE_JPEG:
                            $quality = round((int) $quality);
                            $quality = ($quality > 100) ? 100 : (($quality < 0) ? 0 : $quality);
                            header('Content-Type: image/jpeg');
                            imagejpeg($this->image, null, $quality);
                        break;

                        case IMAGETYPE_PNG:
                            header('Content-Type: image/png');
                            imagepng($this->image);
                        break;

                        case IMAGETYPE_GIF:
                            header('Content-Type: image/gif');
                            imagegif($this->image);
                        break;
                    }

                break;
                case 'imagick':

                    switch ($this->imageType) {
                        case IMAGETYPE_JPEG:
                            header('Content-Type: image/jpeg');
                            echo $this->image->getImageBlob();
                        break;

                        case IMAGETYPE_PNG:
                            header('Content-Type: image/png');
                            echo $this->image->getImageBlob();
                        break;

                        case IMAGETYPE_GIF:
                            header('Content-Type: image/gif');
                            echo $this->image->getImageBlob();
                        break;
                    }

                break;

            }
        }

        /**
         * Saves the image.
         *
         * @param string $filename
         * @param int    $quality  Quality of image compression
         *
         * @return object
         */
        public function save($filename, $quality = 100)
        {
            if ($this->image == null) {
                throw new \Exception('Image not loaded');
            }

            $quality = (int) $quality;
            $quality = ($quality > 100) ? 100 : (($quality < 0) ? 0 : $quality);

            switch ($this->libType) {

                case 'gd':

                    switch ($this->imageType) {
                        case IMAGETYPE_JPEG:

                            if (imagejpeg($this->image, $filename, $quality)) {
                                $this->imageFile = $filename;
                            } else {
                                throw new \Exception('Save error (GD2/JPEG)');
                            }
                        break;

                        case IMAGETYPE_PNG:
                            if (imagepng($this->image, $filename)) {
                                $this->imageFile = $filename;
                            } else {
                                throw new \Exception('Save error (GD2/PNG)');
                            }
                        break;

                        case IMAGETYPE_GIF:
                            if (imagegif($this->image, $filename)) {
                                $this->imageFile = $filename;
                            } else {
                                throw new \Exception('Save error (GD2/GIF)');
                            }
                        break;
                    }

                break;

                case 'imagick':

                    $this->image->setImageCompressionQuality($quality);

                    if (!$this->image->writeImage($filename)) {
                        throw new \Exception('Save error (Imagick)');
                    }

                break;
            }

            return $this;
        }

        /**
         * Imagick scales an image (static method).
         *
         * @param string $infile  Input file
         * @param string $outfile Output file
         * @param int    $width   New width of the image
         * @param int    $height  New height of the image
         * @param int    $quality Quality of image compression
         * @param bool   $aratio  Aspect ratio
         * @param [type] $filter  Imagick filter
         *
         * @return bool
         */
        public static function imagick_resize($infile, $outfile, $width, $height, $quality = 100, $aratio = true, $filter = \Imagick::FILTER_LANCZOS)
        {
            if (!file_exists($infile)) {
                throw new \Exception('File not found');
            }

            $image = new \Imagick();

            $image->readImage($infile);

            $ext = pathinfo($outfile, PATHINFO_EXTENSION);

            if (strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg') {
                $image->setImageFormat('jpg');
                $image->setImageCompression(true);
                $image->setImageCompression(\Imagick::COMPRESSION_JPEG);
                $image->setImageCompressionQuality($quality);
                $image->setOption('jpeg:size', intval($width * 2).'x'.intval($height * 2));
            }

            $image->resizeImage($width, $height, $filter, 1, $aratio);
            $image->writeImage($outfile);
            $image->clear();
            $image->destroy();
            unset($image);

            return true;
        }

        /**
         * Fast scales an image (cli convert command using imagemagick - static method).
         *
         * @param string $infile  Input file
         * @param string $outfile Output file
         * @param int    $width   New width of the image
         * @param int    $height  New height of the image
         * @param int    $quality Quality of image compression
         * @param string $color   background color
         *
         * @return Returns the last line of the command output on success, and false on failure.
         */
        public static function imagick_thumbnail($infile, $outfile, $width = 100, $height = 100, $quality = 100, $color = 'transparent')
        {
            return system('convert -define jpeg:size='.intval($width * 2).'x'.intval($height * 2).' '.$infile.' -thumbnail \''.intval($width).'x'.intval($height).'>\' -background '.$color.' -gravity center -extent '.intval($width).'x'.intval($height).' '.$outfile);
        }

        /**
         * Very fast scales an image (cli command using epeg - static method).
         *
         * @param string $infile  Input file
         * @param string $outfile Output file
         * @param int    $width   New width of the image
         * @param int    $height  New height of the image
         * @param int    $quality Quality of image compression
         * @param bool   $aratio  Apsect ratio
         *
         * @return Returns the last line of the command output on success, and false on failure.
         */
        public static function epeg_resize($infile, $outfile, $width, $height, $quality = 100, $aratio = true)
        {
            if (!file_exists($infile)) {
                throw new \Exception('File not found');
            }

            if ($aratio) {
                $earg = ' -m '.$width.','.$height;
            } else {
                $earg = ' -w '.$width.' -h '.$height;
            }

            $earg .= ' -q '.$quality;

            system('epeg '.$earg.' '.$infile.' '.$outfile);
        }
}
