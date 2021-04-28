<?php

class FileUploadHandler {
  protected $name;
  protected $extensions;
  protected $max_image_size;
  protected $errors = array();
  protected $saved_file;
  
  public static $extension_images = array("png","jpg","jpeg","gif","tiff","bmp");
  public static $extension_docs = array("doc","xls","pst","ppt","pdf","docx","xlsx","pstx","pptx","txt","rtf",
    "dot","xml","docm","dotx","dotm", "xlw","xlt","pps","pot", "sda", "pptm", "potx", "potm", "odt","sxc", "stc", "xlt",
    "uop", "pot", "sxi", "sti");
    
  public static $extension_slip = array("png","jpg","jpeg","git","tiff","bmp","zip", "doc", "ppt", "pdf");
  /**
   * @param $name form field name
   * @param $extension array of extension allowed, without dot
   * @param $maxsize long
   */
  public function __construct($name, $extensions=array(), $maxsize=null) {
    $this->name = $name;
    $this->max_image_size = $maxsize;
    $this->extensions = is_string($extensions) ? explode(",",$extensions): $extensions;
  }
  
  public function getFilePath() {
    $name = $this->name;
    if (isset($_FILES[$name]) && !empty($_FILES[$name]['tmp_name']))
    {
      if (! is_uploaded_file($_FILES[$name]["tmp_name"])) {
        return null; 
      }
      $max_size = $this->max_image_size;
      if (!empty($max_size) && $_FILES[$name]['size'] > (int)$max_size) {
        throw new Exception(lang("file_size_exceed"));
      }
      
      $original_name = $_FILES[$name]["name"];
      $names = explode(".", $original_name);
      $extension = end($names);
      //echo "ext: " . $extension . ", list: " . print_r($this->extensions,true);
      if (!empty($this->extensions)) {
        if (! in_array($extension, $this->extensions)) {
          throw new Exception(lang("file_invalid_extension"));
        }
      }
      
      return $_FILES[$name]["tmp_name"];
    }
    return null;
  }
  
  public function getFileExtension() {
    $filename = $this->getFileName();
    if (empty($filename)) return null;
    $names = explode(".", $filename);
    $extension = end($names);
    return $extension;
  }
  
  public function getFileName() {
    $name = $this->name;
    if (isset($_FILES[$name]) && !empty($_FILES[$name]['tmp_name']))
    {
      if (! is_uploaded_file($_FILES[$name]["tmp_name"])) {
        return null; 
      }
      return $_FILES[$name]["name"]; 
    }
  }
  
  public function doUpload($saveto, $prefix="PS") {
    $name = $this->name;
    try {
      Tools::log(__METHOD__ . ": " . print_r($_FILES[$name],true));
      if (isset($_FILES[$name]) && !empty($_FILES[$name]['tmp_name']))
      {
        if (! is_uploaded_file($_FILES[$name]["tmp_name"])) {
          throw new Exception("Not uploaded"); 
        }
        // Check image validity
        $max_size = Tools::getMaxUploadSize($this->max_image_size);
        if ($_FILES[$name]['size'] > (int)$max_size) {
          throw new Exception("File size exceeded");
        }
         
        $original_name = $_FILES[$name]["name"];
        $names = explode(".", $original_name);
        $extension = end($names);
        if (!empty($this->extensions)) {
          if (! in_array($extension, $this->extensions)) {
            throw new Exception("Invalid extension");
          }
        }
        
        if (empty($saveto))
          throw new Exception("Invalid save-to");
        $destination_path = $saveto;
        $tmp_name = tempnam($destination_path, $prefix);
        if (!$tmp_name) {
          throw new Exception("Unable to get temporary name");
        }
        
        $tmp_name .= ".".$extension;
        
        if (!move_uploaded_file($_FILES[$name]['tmp_name'], $tmp_name)) {
          throw new Exception("Unable to move file");
        }
        
        $this->saved_file = $tmp_name;
        return true;
      }
      
      $this->errors[] = "No file uploaded";
      return false;
    } catch (Exception $e) {
      $this->errors[] = $e->getMessage();
      return false;
    }
  }
  
  public function getSavedName() {
    return basename($this->saved_file);
  }
  
  public function getSavedPath() {
    return dirname($this->saved_file);
  }
  
  public function getError() {
    if (count($this->errors) > 0) {
      return $this->errors[0];
    }
    return null;
  }
  
  public function getErrors() {
    return $this->errors;
  }

  public function doResize($width, $height) {
    //todo
    return false;
    
    // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
    if (!ImageManager::checkImageMemoryLimit($tmp_name))
      $this->errors[] = Tools::displayError('This image cannot be loaded due to memory limit restrictions, please increase your memory_limit value on your server configuration.');
    
    // Copy new image
    if (empty($this->errors) && !ImageManager::resize($tmp_name, _PS_IMG_DIR_.$dir.$id.'.'.$this->imageType, (int)$width, (int)$height, ($ext ? $ext : $this->imageType)))
      $this->errors[] = Tools::displayError('An error occurred while uploading image.');
  
  }
  
}
