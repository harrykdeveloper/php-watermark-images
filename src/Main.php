<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Main
 *
 * @author developer pc
 */

namespace harrykdeveloper\Watermark;

class Main {

    /**
     *
     * @var type | array
     */
    protected $config = array();

    /**
     *
     * @var type | general errors
     */
    protected $errors = [];

    /**
     *
     * @var type | success files
     */
    protected $success = [];

    /**
     *
     * @var type | failed files
     */
    protected $fails = [];

    /**
     * 
     * @param type $config | array | override
     * @return $this
     */
    public function __construct($config = array()) {
        $this->config['position'] = 'center';
        //$this->config['margin_right'] = 10;
        //$this->config['margin_bottom'] = 10;
        $this->config['padding'] = 10;
        $this->config['watermark_image'] = 'watermark.png';
        $this->config['original_directory'] = 'original/';
        $this->config['converted_directory'] = 'converted/';
        $this->config['allowed_extentions'] = array('jpg', 'jpeg', 'png');

        return $this;
    }

    /**
     * 
     * @param type $config | array
     * @return $this
     */
    public function setConfig($config = array()) {
        if (isset($config['padding']))
            $this->config['padding'] = $config['padding'];

        if (isset($config['position']))
            $this->config['position'] = $config['position'];

        /* if (isset($config['margin_right']))
          $this->config['margin_right'] = $config['margin_right'];

          if (isset($config['margin_bottom']))
          $this->config['margin_bottom'] = $config['margin_bottom']; */

        if (isset($config['watermark_image']))
            $this->config['watermark_image'] = $config['watermark_image'];

        if (isset($config['original_directory']))
            $this->config['original_directory'] = $config['original_directory'];

        if (isset($config['converted_directory']))
            $this->config['converted_directory'] = $config['converted_directory'];

        return $this;
    }

    /**
     * 
     * @param type $image
     */
    public function convert($image) {
        try {
            $filExt = explode('.', $image['path']);
            $ext = end($filExt);

            //$getpath = $this->config['original_directory'];
            $getpath = $image['path'];
            $savePath = $this->config['converted_directory'];

            /* create png watermark */
            $stamp = imagecreatefrompng($this->config['watermark_image']);

            if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'JPG' || $ext == 'JPEG') {
                /* create jpg image */
                $im = imagecreatefromjpeg($getpath);
            }

            if ($ext == 'png' || $ext == 'PNG') {
                /* create png image */
                $im = imagecreatefrompng($getpath);
            }

            // Set the margins for the stamp and get the height/width of the stamp image
            //$marge_right = $this->config['margin_right'];
            //$marge_bottom = $this->config['margin_bottom'];

            /* stamp watermark width */
            $sx = imagesx($stamp);

            /* stamp watermark height */
            $sy = imagesy($stamp);

            $image_width = imagesx($im);
            $image_height = imagesy($im);

            $stamp_width = imagesx($stamp);
            $stamp_height = imagesy($stamp);

            switch ($this->config['position']) {
                case 'top-center':
                    $right_margin = ($image_width / 2) - ($stamp_width / 2);
                    $top_margin = (int)$this->config['padding'];
                    break;

                case 'center':
                    $right_margin = ($image_width / 2) - ($stamp_width / 2);
                    $top_margin = ($image_height / 2) - ($stamp_height / 2);
                    break;

                case 'bottom-center':
                    $right_margin = ($image_width / 2) - ($stamp_width / 2);
                    $top_margin = ($image_height - $stamp_height - (int)$this->config['padding']);
                    break;

                case 'left-top':
                    $right_margin = (int)$this->config['padding'];
                    $top_margin = (int)$this->config['padding'];
                    break;

                case 'left-center':
                    $right_margin = (int)$this->config['padding'];
                    $top_margin = ($image_height / 2) - ($stamp_height / 2);
                    break;

                case 'left-bottom':
                    $right_margin = (int)$this->config['padding'];
                    $top_margin = ($image_height - $stamp_height - (int)$this->config['padding']);
                    break;

                case 'right-top':
                    $right_margin = ($image_width - $stamp_width - (int)$this->config['padding']);
                    $top_margin = (int)$this->config['padding'];
                    break;

                case 'right-center':
                    $right_margin = ($image_width - $stamp_width - (int)$this->config['padding']);
                    $top_margin = ($image_height / 2) - ($stamp_height / 2);
                    break;

                case 'right-bottom':
                    $right_margin = ($image_width - $stamp_width - (int)$this->config['padding']);
                    $top_margin = ($image_height - $stamp_height - (int)$this->config['padding']);
                    break;
            }

            // Copy the stamp image onto our photo using the margin offsets and the photo 
            // width to calculate positioning of the stamp. 
            //imagecopy($im, $stamp, (imagesx($im) - $sx - $marge_right), (imagesy($im) - $sy - $marge_bottom), 0, 0, imagesx($stamp), imagesy($stamp));
            imagecopy($im, $stamp, $right_margin, $top_margin, 0, 0, imagesx($stamp), imagesy($stamp));

            // Output and free memory
            /* header('Content-type: image/jpg'); */
            
            $this->config['converted_directory'] = trim($this->config['converted_directory'],'/');
            $this->config['converted_directory'] = trim($this->config['converted_directory'],'\\');

            $savePath = trim(trim($savePath,'/'), "\\") .'/'. $image['dir'];
            
            if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'JPG' || $ext == 'JPEG') {
                //imagejpeg($im, $savePath . urlencode($image['file']));
                if (!is_dir($this->config['converted_directory'] .'/'. $image['dir'])) {
                    mkdir($this->config['converted_directory'] .'/'. $image['dir'], 755, TRUE);
                }
                
                if (imagejpeg($im, $savePath . '/' . ($image['file'])) === true) {
                    $this->success[] = $image;
                } else {
                    $this->fails[] = $image;
                }
            }

            if ($ext == 'png') {
                if (!is_dir($this->config['converted_directory'] .'/'. $image['dir'])) {
                    mkdir($this->config['converted_directory'] .'/'. $image['dir'], 755, TRUE);
                }
                //imagepng($im, $savePath . urlencode($image['file']));
                if (imagepng($im, $savePath . '/' . ($image['file'])) === true) {
                    $this->success[] = $image;
                } else {
                    $this->fails[] = $image;
                }
            }

            imagedestroy($im);
            
        } catch (\Exception $ex) {echo $ex->getMessage();
            $this->fails[] = $image;
        }
    }

    /**
     * General Errors
     * 
     * @return type | array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Get values of files with successful watermark
     * 
     * @return type | array
     */
    public function getSuccess() {
        return $this->success;
    }

    /**
     * Get values of files with unsuccessful watermark
     * 
     * @return type | array
     */
    public function getFails() {
        return $this->fails;
    }

    /**
     * 
     * @throws \Exception
     * @throws Exception
     */
    public function convertAll() {
        try {

            $getpath = $this->config['original_directory'];
            $savePath = $this->config['converted_directory'];

            if (!is_dir($getpath)) {
                throw new \Exception("Invalid Directory to Convert Images");
            }

            if (!is_dir($savePath)) {
                throw new \Exception("Invalid Directory to save Converted Images");
            }

            if (!file_exists($this->config['watermark_image'])) {
                throw new \Exception("Invalid Watermrk image or image not found");
            }

            if (empty($this->config['position'])) {
                throw new \Exception("Invalid Watermrk position or empty");
            }

            $file = $getpath;
            if (!file_exists($file))
                throw new \Exception($file . ' File not exist!');

            $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($file));

            $files = [];
            foreach ($rii as $file) {
                if ($file->isDir()) {
                    continue;
                }
                $filExt = explode('.', $file->getPathname());
                $ext = end($filExt);

                if (in_array(strtolower($ext), $this->config['allowed_extentions']) == TRUE) {
                    $files[] = ['dir' => $file->getPath(), 'file' => $file->getBasename(), 'path' => $file->getPathname()];
                }
            }

            if (sizeof($files) <= 0) {
                throw new \Exception("No image found to apply Watermark!");
            }

            $k = 1;
            foreach ($files as $f) {
                if($k % 15 == 0){
                    sleep(2);
                }
                $this->convert($f);
                $k++;
            }

            return;
            
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

}
