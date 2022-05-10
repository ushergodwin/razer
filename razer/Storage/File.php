<?php
namespace Razer\Storage;


/**
 * File
 * Upload and saving it to database
 */
class File
{
    protected $allowedFileExtensions = array('jpg','png','jpeg','gif', 'pdf', 'docx', 'ppt');

    protected $input_name = '';

    protected $file_name;


    public function __construct($key, $defualt)
    {
        $this->input_name = $this->hasFile($key) ? $key : $defualt;
        return $this;
    }
    /**
     * Determine if the uploaded data contains a file
     *
     * @param string $key
     * @return boolean
     */
    public function hasFile($key) {
        return in_array($key, $_FILES);
    }


    /**
     * Store a the uploaded file on the filesystem disk
     *
     * @param string $path
     * @param string $name
     * @param array $options Filesystem options
     * 
     * StatusCode 0 Upload failed
     * 
     * StatusCode 1 Upload successful
     * 
     * StatusCode 2 Some files have extensions that are not allowed
     * 
     * StatusCode 3 The input is empty
     * @return int
     */
    public function storeAs(string $path, string $name, array $options = []){
        $this->file_name = $name;
        return $this->store($path, $options);
    }


    /**
     * Allowed File extensions for upload
     *
     * @param string|array $extensions
     * 
     * defaults to array('jpg','png','jpeg','gif', 'pdf', 'docx', 'ppt')
     * @return void
     */
    public function allowedFileExtensions($extensions)
    {
        $this->allowedFileExtensions = $extensions;
        return $this;
    }


    /**
     * @param bool $array_to_string True if an array of file names should be imploaded.
     * @return string|array Original file name or an indexed array of original file names
     */
    public function getClientOriginalName(bool $array_to_string = false) {
        if(!is_array($_FILES[$this->input_name]['name']))
        {
            return $_FILES[$this->input_name]["name"];
        }

        if($array_to_string)
        {
            return implode(',', $_FILES[$this->input_name]['name']);
        }
        return array_filter($_FILES[$this->input_name]['name']);
    }

    /**
     * Store a the uploaded file on the filesystem disk
     *
     * @param string $dir The directory where to upload files
     * @param array $options 
     * @return int
     * 
     * StatusCode 0 Upload failed
     * 
     * StatusCode 1 Upload successful
     * 
     * StatusCode 2 Some files have extensions that are not allowed
     * 
     * StatusCode 3 The input is empty
     */
    public function store(string $path, array $options = [])
    {
            // File upload configuration 
            $root = $path;
            if(!empty($options) && in_array('disk', $options))
            {
                $storage = include_once base_path('config/filesystem.php');
                $root = $path . $storage['disks'][$options['disk']]['root'];
            }
            $targetDir = $root . $path ."/"; 
            if(!file_exists($targetDir))
            {
                $directory = explode('/', $path);
                $dir_count = count($directory);
                for ($i=0; $i < $dir_count; $i++) { 
                    # code...
                    $dir_path = $root . $directory[$i];
                    $root = $dir_path . "/";
                    if(!file_exists($dir_path))
                    {
                        mkdir($dir_path);
                    }
                }
            }

            $allowTypes = $this->allowedFileExtensions; 
            
            $statusCode = 5;
            $uploaded_files = $_FILES[$this->input_name]['name'];
            if(!is_array($uploaded_files))
            {
                $target_file = $targetDir . basename(
                    empty($this->file_name) ? $_FILES[$this->input_name]["name"] : 
                    $this->file_name
                );

                $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                if(!in_array($fileType, $allowTypes)){ 
                    return 2;
                }
                if (!move_uploaded_file($_FILES[$this->input_name]["tmp_name"], $target_file)) {
                    return 0;
                }
                return 1;
            }

            $fileNames = array_filter($_FILES[$this->input_name]['name']);
            $file_count = count($fileNames);
            if(empty($fileNames)){
                return 3;
            }

            if($this->isFileExtensionAllowed($this->input_name, $targetDir))
            {
                for($key = 0; $key < $file_count; $key++){ 
                    // File upload path 
                    $fileName = basename(
                        empty($this->file_name) ? $_FILES[$this->input_name]['name'][$key] :
                        $this->file_name[$key]
                    ); 

                    $targetFilePath = $targetDir . $fileName; 
                    
                    // Upload file to server 
                    if(move_uploaded_file($_FILES[$this->input_name]["tmp_name"][$key], $targetFilePath)){ 
                        // Image db insert sql 
                        $statusCode = 1;
                    }else{ 
                        $statusCode = 0;
                    }  
                } 

            }else{ 
                $statusCode = 2; 
            } 
        return $statusCode;
    }

    protected function isFileExtensionAllowed($targetDir)
    {
        $fileNames = array_filter($_FILES[$this->input_name]['name']);
        $file_count = count($fileNames);
        for($key = 0; $key < $file_count; $key++){ 
            // File upload path 
            $fileName = basename($_FILES[$this->input_name]['name'][$key]); 
            $targetFilePath = $targetDir . $fileName; 
            
            // Check whether file type is valid 
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            if(!in_array($fileType, $this->allowedFileExtensions)){ 
                return false;
            }
        }
        return true;
    }


    /**
     * Get an array of uploaded file names for inserting in the database
     *
     * @param string $column_name The column name corresponding files
     * @param string $this->input_name The name of the input that submitted
     * @return array
     * 
     * A associative array returned is in form of array(id => id_no, column_name => file_name)
     * 
     * The order of the files is returned as you selected them
     * 
     * Loop through the array to get the data
     */
    public function getFilesForDatabaseStorage(string $column_name) {
        $fileNames = array_filter($_FILES[$this->input_name]['name']);

        $filesArray = array();

        if(!empty($fileNames)){
            $file_count = count($fileNames);
            for($i = 0; $i < $file_count; $i++){
                $fileName = basename($_FILES[$this->input_name]['name'][$i]);
                $filesArray[] = array(
                    "id" => $i,
                    $column_name => $fileName
                );
            }
        }
        return $filesArray;
    }
}
