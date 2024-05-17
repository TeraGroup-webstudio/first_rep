<?php

class ImageWatermark {
    private $file;
    private $image;
    private $width;
    private $height;
    private $bits;
    private $mime;

    public function __construct($file) {
        if (file_exists($file)) {
            $this->file = $file;

            $info = getimagesize($file);

            $this->width  = $info[0];
            $this->height = $info[1];
            $this->bits = isset($info['bits']) ? $info['bits'] : '';
            $this->mime = isset($info['mime']) ? $info['mime'] : '';

            if ($this->mime == 'image/gif') {
                $this->image = imagecreatefromgif($file);
            } elseif ($this->mime == 'image/png') {
                $this->image = imagecreatefrompng($file);
            } elseif ($this->mime == 'image/jpeg') {
                $this->image = imagecreatefromjpeg($file);
            }
        } else {
            exit('Error: Could not load image ' . $file . '!');
        }
    }

    public function getFile() {
        return $this->file;
    }

    public function getImage() {
        return $this->image;
    }

    public function getWidth() {
        return $this->width;
    }

    public function getHeight() {
        return $this->height;
    }

    public function getBits() {
        return $this->bits;
    }

    public function getMime() {
        return $this->mime;
    }

    public function save($file, $quality = 90) {
        $info = pathinfo($file);

        $extension = strtolower($info['extension']);

        if (is_resource($this->image)) {
            if ($extension == 'jpeg' || $extension == 'jpg') {
                imagejpeg($this->image, $file, $quality);
            } elseif ($extension == 'png') {
                imagepng($this->image, $file);
            } elseif ($extension == 'gif') {
                imagegif($this->image, $file);
            }

            imagedestroy($this->image);
        }
    }

    public function resize($width = 0, $height = 0, $default = '') {
        if (!$this->width || !$this->height) {
            return;
        }

        $xpos = 0;
        $ypos = 0;
        $scale = 1;

        $scale_w = $width / $this->width;
        $scale_h = $height / $this->height;

        if ($default == 'w') {
            $scale = $scale_w;
        } elseif ($default == 'h') {
            $scale = $scale_h;
        } else {
            $scale = min($scale_w, $scale_h);
        }

        if ($scale == 1 && $scale_h == $scale_w && $this->mime != 'image/png') {
            return;
        }

        $new_width = (int)($this->width * $scale);
        $new_height = (int)($this->height * $scale);
        $xpos = (int)(($width - $new_width) / 2);
        $ypos = (int)(($height - $new_height) / 2);

        $image_old = $this->image;
        $this->image = imagecreatetruecolor($width, $height);

        if ($this->mime == 'image/png') {
            imagealphablending($this->image, false);
            imagesavealpha($this->image, true);
            $background = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
            imagecolortransparent($this->image, $background);
        } else {
            $background = imagecolorallocate($this->image, 255, 255, 255);
        }

        imagefilledrectangle($this->image, 0, 0, $width, $height, $background);

        imagecopyresampled($this->image, $image_old, $xpos, $ypos, 0, 0, $new_width, $new_height, $this->width, $this->height);
        imagedestroy($image_old);

        $this->width = $width;
        $this->height = $height;
    }

    public function watermark($config) {
        $file = DIR_IMAGE . $config['image'];

        $info = getimagesize($file);

        $width  = $info[0];
        $height = $info[1];
        $bits = isset($info['bits']) ? $info['bits'] : '';
        $mime = isset($info['mime']) ? $info['mime'] : '';

        if ($mime == 'image/gif') {
            $watermark = imagecreatefromgif($file);
        } elseif ($mime == 'image/png') {
            $watermark = imagecreatefrompng($file);
        } elseif ($mime == 'image/jpeg') {
            $watermark = imagecreatefromjpeg($file);
        }

        if((float)$config['zoom'] != 1)
        {

            $new_width = (int)($width * (float)$config['zoom']);
            $new_height = (int)($height * (float)$config['zoom']);
            $image_old = $watermark;
            $watermark = imagecreatetruecolor($new_width, $new_height);

            if ($mime == 'image/png') {
                imagealphablending($watermark, false);
                imagesavealpha($watermark, true);
                $background = imagecolorallocatealpha($watermark, 255, 255, 255, 0);
                imagecolortransparent($watermark, $background);
            } else {
                $background = imagecolorallocate($this->image, 255, 255, 255);
            }

            imagefilledrectangle($watermark, 0, 0, $width, $height, $background);

            imagecopyresampled($watermark, $image_old, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagedestroy($image_old);
            $width = $new_width;
            $height = $new_height;
        }

        $watermark_pos_x = 10;
        $watermark_pos_y = 10;

        if((int)$config['pos_x_center'] == 1)
        {
            $watermark_pos_x = (int)($this->width/2 - $width/2);
        }
        elseif((int)$config['pos_x'] < 0)
        {
            $watermark_pos_x = $this->width - $width + (int)$config['pos_x'];
        }
        else
        {
            $watermark_pos_x = (int)$config['pos_x'];
        }

        if((int)$config['pos_y_center'] == 1)
        {
            $watermark_pos_y = (int)($this->height/2 - $height/2);
        }
        elseif((int)$config['pos_y'] < 0)
        {
            $watermark_pos_y = $this->height - $height + (int)$config['pos_y'];
        }
        else
        {
            $watermark_pos_x = (int)$config['pos_y'];
        }

        $watermark_width = $width;
        $watermark_height = $height;
        $opacity = (int)((float)$config['opacity'] * 127);

        $this->imagecopymergealpha($this->image, $watermark, $watermark_pos_x, $watermark_pos_y, 0, 0, $watermark_width, $watermark_height,$opacity);

        imagedestroy($watermark);
    }

    public static function imagecopymergealpha(&$destImg, &$srcImg, $destX, $destY, $srcX, $srcY, $srcW, $srcH, $pct = 0)
    {
        $destX = (int) $destX;
        $destY = (int) $destY;
        $srcX = (int) $srcX;
        $srcY = (int) $srcY;
        $srcW = (int) $srcW;
        $srcH = (int) $srcH;
        $pct = (int) $pct;
        $destW = imageSX($destImg);
        $destH = imageSY($destImg);

        for ($y = 0; $y < $srcH + $srcY; $y++) {

            for ($x = 0; $x < $srcW + $srcX; $x++) {

                if ($x + $destX >= 0 && $x + $destX < $destW && $x + $srcX >= 0 && $x + $srcX < $srcW && $y + $destY >= 0 && $y + $destY < $destH && $y + $srcY >= 0 && $y + $srcY < $srcH) {

                    $destPixel = imageColorsForIndex($destImg, imageColorat($destImg, $x + $destX, $y + $destY));
                    $srcImgColorat = imageColorat($srcImg, $x + $srcX, $y + $srcY);

                    if ($srcImgColorat >= 0) {

                        $srcPixel = imageColorsForIndex($srcImg, $srcImgColorat);

                        $srcAlpha = 1 - ($srcPixel['alpha'] / 127);
                        $destAlpha = 1 - ($destPixel['alpha'] / 127);
                        $opacity = $srcAlpha * $pct / 100;

                        if ($destAlpha >= $opacity) {
                            $alpha = $destAlpha;
                        }

                        if ($destAlpha < $opacity) {
                            $alpha = $opacity;
                        }

                        if ($alpha > 1) {
                            $alpha = 1;
                        }

                        if ($opacity > 0) {

                            $destRed = round((($destPixel['red'] * $destAlpha * (1 - $opacity))));
                            $destGreen = round((($destPixel['green'] * $destAlpha * (1 - $opacity))));
                            $destBlue = round((($destPixel['blue'] * $destAlpha * (1 - $opacity))));
                            $srcRed = round((($srcPixel['red'] * $opacity)));
                            $srcGreen = round((($srcPixel['green'] * $opacity)));
                            $srcBlue = round((($srcPixel['blue'] * $opacity)));
                            $red = round(($destRed + $srcRed  ) / ($destAlpha * (1 - $opacity) + $opacity));
                            $green = round(($destGreen + $srcGreen) / ($destAlpha * (1 - $opacity) + $opacity));
                            $blue = round(($destBlue + $srcBlue ) / ($destAlpha * (1 - $opacity) + $opacity));

                            if ($red   > 255) {
                                $red   = 255;
                            }

                            if ($green > 255) {
                                $green = 255;
                            }

                            if ($blue  > 255) {
                                $blue  = 255;
                            }

                            $alpha = round((1 - $alpha) * 127);
                            $color = imageColorAllocateAlpha($destImg, $red, $green, $blue, $alpha);
                            imageSetPixel($destImg, $x + $destX, $y + $destY, $color);
                        }
                    }
                }
            }
        }
    }

    public function crop($top_x, $top_y, $bottom_x, $bottom_y) {
        $image_old = $this->image;
        $this->image = imagecreatetruecolor($bottom_x - $top_x, $bottom_y - $top_y);

        imagecopy($this->image, $image_old, 0, 0, $top_x, $top_y, $this->width, $this->height);
        imagedestroy($image_old);

        $this->width = $bottom_x - $top_x;
        $this->height = $bottom_y - $top_y;
    }

    public function rotate($degree, $color = 'FFFFFF') {
        $rgb = $this->html2rgb($color);

        $this->image = imagerotate($this->image, $degree, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));

        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);
    }

    private function filter() {
        $args = func_get_args();

        call_user_func_array('imagefilter', $args);
    }

    private function text($text, $x = 0, $y = 0, $size = 5, $color = '000000') {
        $rgb = $this->html2rgb($color);

        imagestring($this->image, $size, $x, $y, $text, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));
    }

    private function merge($merge, $x = 0, $y = 0, $opacity = 100) {
        imagecopymerge($this->image, $merge->getImage(), $x, $y, 0, 0, $merge->getWidth(), $merge->getHeight(), $opacity);
    }

    private function html2rgb($color) {
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }

        if (strlen($color) == 6) {
            list($r, $g, $b) = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
        } elseif (strlen($color) == 3) {
            list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        } else {
            return false;
        }

        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);

        return array($r, $g, $b);
    }
}

class ModelToolImage extends Model {

    public $conf = false;

    public function webpSupport() {
        if ($this->config->get('theme_default_webp_status')) {
            $webp = gd_info();

            if (isset($this->request->server['HTTP_ACCEPT']) && strpos($this->request->server['HTTP_ACCEPT'], 'webp')) {
                $status = true;
            } elseif (isset($this->request->server['HTTP_USER_AGENT']) && strpos($this->request->server['HTTP_USER_AGENT'], 'Firefox')) {
                $status = true;
            } else {
                $status = false;
            }

            if ($webp['WebP Support'] && $status) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function resize($filename, $width, $height, $type = false) {
        if (!is_file(DIR_IMAGE . $filename) || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $filename)), 0, strlen(DIR_IMAGE)) != str_replace('\\', '/', DIR_IMAGE)) {
            return;
        }

        $types = array(
            'category_image' => 'category_image',
            'product_thumb' => 'product_thumb',
            'product_popup' => 'product_popup',
            'product_list' => 'product_list',
            'product_additional' => 'product_additional',
            'product_related' => 'product_related',
            'product_in_compare' => 'product_in_compare',
            'product_in_wish_list' => 'product_in_wish_list',
            'product_in_cart' => 'product_in_cart',
        );

        // Load options
        if(!$this->conf)
        {
            $this->load->model('setting/setting');
            $this->conf = $this->model_setting_setting->getSetting('theme_default');
            $keys = array_keys($this->conf);
            $keys = str_replace('theme_default_watermark_', '', $keys);
            $this->conf = array_combine($keys, array_values($this->conf));
        }

        if(in_array($type, $types) && $this->conf[$type] == 1 && $this->conf['status'] == 1)
        {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $image_old = $filename;
            $image_new = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int)$width . 'x' . (int)$height . '-' . $type . '.' . $extension;
            if (!is_file(DIR_IMAGE . $image_new) || (filectime(DIR_IMAGE . $image_old) > filectime(DIR_IMAGE . $image_new))) {
                $path = '';

                $directories = explode('/', dirname(str_replace('../', '', $image_new)));

                foreach ($directories as $directory) {
                    $path = $path . '/' . $directory;

                    if (!is_dir(DIR_IMAGE . $path)) {
                        @mkdir(DIR_IMAGE . $path, 0777);
                    }
                }

                list($width_orig, $height_orig) = getimagesize(DIR_IMAGE . $image_old);

                $image = new ImageWatermark(DIR_IMAGE . $image_old);
                $image->resize($width, $height);
                $image->watermark($this->conf);
                $image->save(DIR_IMAGE . $image_new);
            }

            $image_webp = false;
            $webp_support = $this->webpSupport();
            if(!isset($type)){$type='';}
            if ($webp_support && strtolower($extension) != 'gif') {
                error_reporting(0);
                ini_set('display_errors', 0);
                $image_webp = 'cache/webp/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int)$width . 'x' . (int)$height . $type . '.webp';
                if (!is_file(DIR_IMAGE . $image_webp) || (filemtime(DIR_IMAGE . $image_new) > filemtime(DIR_IMAGE . $image_webp))) {
                    $path = '';
                    $directories = explode('/', dirname($image_webp));
                    foreach ($directories as $directory) {
                        $path = $path . '/' . $directory;
                        if (!is_dir(DIR_IMAGE . $path)) {
                            @mkdir(DIR_IMAGE . $path, 0777);
                        }
                    }
                    if (strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg') {
                        $image_default = imagecreatefromjpeg(DIR_IMAGE . $image_new);
                    }
                    elseif (strtolower($extension) == 'png') {
                        $image_default = imagecreatefrompng(DIR_IMAGE . $image_new);
                    }
                    imagewebp($image_default, DIR_IMAGE . $image_webp, ($this->config->get('theme_default_webp_webp_quality') ? $this->config->get('theme_default_webp_webp_quality') : 80));
                    imagedestroy($image_default);
                    if (filesize(DIR_IMAGE . $image_webp) % 2 == 1) {
                        file_put_contents(DIR_IMAGE . $image_webp, "\0", FILE_APPEND);
                    }
                }
            }
            if ($webp_support && is_file(DIR_IMAGE . $image_webp)){
                $image_new = $image_webp;
            }

            $image_new = str_replace(' ', '%20', $image_new);  // fix bug when attach image on email (gmail.com). it is automatic changing space " " to +

            if (mime_content_type(DIR_IMAGE . $image_old) != 'image/svg+xml' && in_array($extension, array('svg', 'SVG'))) {
                $dom = new DOMDocument;
                $dom->loadXML(file_get_contents(DIR_IMAGE . $image_old));

                if ($dom) {
                    $svg = simplexml_import_dom($dom);
                }
            } elseif (mime_content_type(DIR_IMAGE . $image_old) == 'image/svg+xml') {
                $svg = simplexml_load_file(DIR_IMAGE . $image_old);
            }

            if (isset($svg)) {
                if ($svg['width'] && $svg['height']) {
                    $width_orig = (string)$svg['width'];
                    $height_orig = (string)$svg['height'];

                    if (is_numeric($width_orig) && is_numeric($height_orig)) {
                        $width_orig = (string)$svg['width'];
                        $height_orig = (string)$svg['height'];
                    } elseif (substr($width_orig, -2) == 'px' && substr($height_orig, -2) == 'px') {
                        $width_orig = str_replace('px', '', $width_orig);
                        $height_orig = str_replace('px', '', $height_orig);

                        if (!is_numeric($width_orig) && !is_numeric($height_orig)) {
                            $width_orig = '';
                            $height_orig = '';
                        }
                    }
                } elseif ($svg['viewBox']) {
                    $viewbox = explode(' ', $svg['viewBox']);

                    $height_orig = array_pop($viewbox);
                    $width_orig = array_pop($viewbox);
                } else {
                    $width_orig = '';
                    $height_orig = '';
                }

                if (($width_orig && $height_orig) && ($width_orig != $width || $height_orig != $height)) {
                    $scale_w = $width / $width_orig;
                    $scale_h = $height / $height_orig;

                    $scale = min($scale_w, $scale_h);

                    $new_width = (int)($width_orig * $scale);
                    $new_height = (int)($height_orig * $scale);

                    $svg['width'] = $new_width;
                    $svg['height'] = $new_height;

                    $svg->asXML(DIR_IMAGE . $image_new);
                } else {
                    $svg['width'] = $width;
                    $svg['height'] = $height;

                    $svg->asXML(DIR_IMAGE . $image_new);
                }
            }

            if ($this->request->server['HTTPS']) {
                return $this->config->get('config_ssl') . 'image/' . $image_new;
            } else {
                return $this->config->get('config_url') . 'image/' . $image_new;
            }

        } else {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);

            $image_old = $filename;
            $image_new = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int)$width . 'x' . (int)$height . '.' . $extension;

            if (!is_file(DIR_IMAGE . $image_new) ) {
                list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $image_old);
                if (!in_array($image_type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF)) && $image_type) {
                    /*if($image_type == '18'){
                        return 'image/'.$image_old;
                    } else {
                        return DIR_IMAGE . $image_old;
                    }*/
                    if ($this->request->server['HTTPS']) {
                        return $this->config->get('config_ssl') . 'image/' . $image_old;
                    } else {
                        return $this->config->get('config_url') . 'image/' . $image_old;
                    }

                }

                $path = '';

                $directories = explode('/', dirname($image_new));

                foreach ($directories as $directory) {
                    $path = $path . '/' . $directory;

                    if (!is_dir(DIR_IMAGE . $path)) {
                        @mkdir(DIR_IMAGE . $path, 0777);
                    }
                }

                if ($width_orig != $width || $height_orig != $height) {
                    $image = new Image(DIR_IMAGE . $image_old);
                    $image->resize($width, $height);
                    $image->save(DIR_IMAGE . $image_new);
                } else {
                    copy(DIR_IMAGE . $image_old, DIR_IMAGE . $image_new);
                }
            }
            //exit();
            $image_webp = false;
            $webp_support = $this->webpSupport();
            if(!isset($type)){$type='';}
            if ($webp_support && strtolower($extension) != 'gif') {
                error_reporting(0);
                ini_set('display_errors', 0);
                $image_webp = 'cache/webp/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int)$width . 'x' . (int)$height . $type . '.webp';
                if (!is_file(DIR_IMAGE . $image_webp) || (filemtime(DIR_IMAGE . $image_new) > filemtime(DIR_IMAGE . $image_webp))) {
                    $path = '';
                    $directories = explode('/', dirname($image_webp));
                    foreach ($directories as $directory) {
                        $path = $path . '/' . $directory;
                        if (!is_dir(DIR_IMAGE . $path)) {
                            @mkdir(DIR_IMAGE . $path, 0777);
                        }
                    }
                    if (strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg') {
                        $image_default = imagecreatefromjpeg(DIR_IMAGE . $image_new);
                    }
                    elseif (strtolower($extension) == 'png') {
                        $image_default = imagecreatefrompng(DIR_IMAGE . $image_new);
                    }
                    imagewebp($image_default, DIR_IMAGE . $image_webp, ($this->config->get('theme_default_webp_webp_quality') ? $this->config->get('theme_default_webp_webp_quality') : 80));
                    imagedestroy($image_default);
                    if (filesize(DIR_IMAGE . $image_webp) % 2 == 1) {
                        file_put_contents(DIR_IMAGE . $image_webp, "\0", FILE_APPEND);
                    }
                }
            }
            if ($webp_support && is_file(DIR_IMAGE . $image_webp)){
                $image_new = $image_webp;
            }

            $image_new = str_replace(' ', '%20', $image_new);  // fix bug when attach image on email (gmail.com). it is automatic changing space " " to +

            if (mime_content_type(DIR_IMAGE . $image_old) != 'image/svg+xml' && in_array($extension, array('svg', 'SVG'))) {
                $dom = new DOMDocument;
                $dom->loadXML(file_get_contents(DIR_IMAGE . $image_old));

                if ($dom) {
                    $svg = simplexml_import_dom($dom);
                }
            } elseif (mime_content_type(DIR_IMAGE . $image_old) == 'image/svg+xml') {
                $svg = simplexml_load_file(DIR_IMAGE . $image_old);
            }

            if (isset($svg)) {
                if ($svg['width'] && $svg['height']) {
                    $width_orig = (string)$svg['width'];
                    $height_orig = (string)$svg['height'];

                    if (is_numeric($width_orig) && is_numeric($height_orig)) {
                        $width_orig = (string)$svg['width'];
                        $height_orig = (string)$svg['height'];
                    } elseif (substr($width_orig, -2) == 'px' && substr($height_orig, -2) == 'px') {
                        $width_orig = str_replace('px', '', $width_orig);
                        $height_orig = str_replace('px', '', $height_orig);

                        if (!is_numeric($width_orig) && !is_numeric($height_orig)) {
                            $width_orig = '';
                            $height_orig = '';
                        }
                    }
                } elseif ($svg['viewBox']) {
                    $viewbox = explode(' ', $svg['viewBox']);

                    $height_orig = array_pop($viewbox);
                    $width_orig = array_pop($viewbox);
                } else {
                    $width_orig = '';
                    $height_orig = '';
                }

                if (($width_orig && $height_orig) && ($width_orig != $width || $height_orig != $height)) {
                    $scale_w = $width / $width_orig;
                    $scale_h = $height / $height_orig;

                    $scale = min($scale_w, $scale_h);

                    $new_width = (int)($width_orig * $scale);
                    $new_height = (int)($height_orig * $scale);

                    $svg['width'] = $new_width;
                    $svg['height'] = $new_height;

                    $svg->asXML(DIR_IMAGE . $image_new);
                } else {
                    $svg['width'] = $width;
                    $svg['height'] = $height;

                    $svg->asXML(DIR_IMAGE . $image_new);
                }
            }

            if ($this->request->server['HTTPS']) {
                return $this->config->get('config_ssl') . 'image/' . $image_new;
            } else {
                return $this->config->get('config_url') . 'image/' . $image_new;
            }
        }



    }
}
