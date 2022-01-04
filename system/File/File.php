<?php
namespace System\File;


/**
 * File
 * Upload and saving it to database
 */
class File
{
    protected $allowedFileExtensions = array('jpg','png','jpeg','gif', 'pdf', 'docx', 'ppt');


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
    }


    /**
     * @param string $input_name The name of the input file
     * use it to save the image reference in the database
     * @param bool $array_to_string True if an array of file names should be imploaded.
     * @return string|array A file name or an indexed array of file names
     */
    public function name($input_name, bool $array_to_string = false) {
        if(!is_array($_FILES[$input_name]['name']))
        {
            return $_FILES[$input_name]["name"];
        }

        if($array_to_string)
        {
            return implode(',', $_FILES[$input_name]['name']);
        }
        return array_filter($_FILES[$input_name]['name']);
    }

    /**
     * Upload Files to server
     *
     * @param string $dir The directory where to upload files
     * @param string $input_name The name of the input submitted
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
    public function move(string $dir, string $input_name)
    {
            // File upload configuration 
            $root = $_SERVER['DOCUMENT_ROOT']."/public/";
            $targetDir = $root . $dir ."/"; 
            if(!file_exists($targetDir))
            {
                $directory = explode('/', $dir);
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
            $uploaded_files = $_FILES[$input_name]['name'];
            if(!is_array($uploaded_files))
            {
                $target_file = $targetDir . basename($_FILES[$input_name]["name"]);
                $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                if(!in_array($fileType, $allowTypes)){ 
                    return 2;
                }
                if (!move_uploaded_file($_FILES[$input_name]["tmp_name"], $target_file)) {
                    return 0;
                }
                return 1;
            }

            $fileNames = array_filter($_FILES[$input_name]['name']);
            $file_count = count($fileNames);
            if(empty($fileNames)){
                return 3;
            }

            if($this->isFileExtensionAllowed($input_name, $targetDir))
            {
                for($key = 0; $key < $file_count; $key++){ 
                    // File upload path 
                    $fileName = basename($_FILES[$input_name]['name'][$key]); 
                    $targetFilePath = $targetDir . $fileName; 
                    
                    // Check whether file type is valid 
                    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION)); 
                        // Upload file to server 
                    if(move_uploaded_file($_FILES[$input_name]["tmp_name"][$key], $targetFilePath)){ 
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

    protected function isFileExtensionAllowed($input_name, $targetDir)
    {
        $fileNames = array_filter($_FILES[$input_name]['name']);
        $file_count = count($fileNames);
        for($key = 0; $key < $file_count; $key++){ 
            // File upload path 
            $fileName = basename($_FILES[$input_name]['name'][$key]); 
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
     * @param string $input_name The name of the input that submitted
     * @return array
     * 
     * A associative array returned is in form of array(id => id_no, column_name => file_name)
     * 
     * The order of the files is returned as you selected them
     * 
     * Loop through the array to get the data
     */
    public function getFilesForDatabaseStorage(string $column_name, string $input_name) {
        $fileNames = array_filter($_FILES[$input_name]['name']);

        $filesArray = array();

        if(!empty($fileNames)){
            $file_count = count($fileNames);
            for($i = 0; $i < $file_count; $i++){
                $fileName = basename($_FILES[$input_name]['name'][$i]);
                $filesArray[] = array(
                    "id" => $i,
                    $column_name => $fileName
                );
            }
        }
        return $filesArray;
    }
}
