# PHP Script To Watermark Images and Maintain Directory Structure

## Example Usage
    <?php

    /* 
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */

    include_once __DIR__.'/../vendor/autoload.php';
    error_reporting(0);
    ini_set('max_execution_time', '600');

    use harrykdeveloper\Watermark\Main;

    $watermark = new Main();

    $watermark->setConfig([
        /**
        * Positions
        * 
        * top-center | center | bottom-center | 
        * left-top | left-center | left-bottom | right-top | 
        * right-center | right-bottom
        */
        'position' => 'right-bottom',
        // full image path.
        'watermark_image' => 'watermark.png',
        // path from where files will be copied.
        'original_directory' => 'original',
        // path where files are copied after watermark implementation. 
        'converted_directory' => 'converted',
        // padding from image sides
        'padding' => 10
    ])->convertAll();

    /**
     * Errors
     */
    if($watermark->getErrors()){
        echo '<pre>Errors:';
        print_r($watermark->getErrors());
        echo '<pre/>';
    }

    /**
     * Success Watermark
     */
    if($watermark->getSuccess()){
        echo '<pre>Success:';
        print_r($watermark->getSuccess());
        echo '<pre/>';
    }

    /**
     * Failed images.
     */
    if($watermark->getFails()){
        echo '<pre>Fails:';
        print_r($watermark->getFails());
        echo '<pre/>';
    }
    ?>