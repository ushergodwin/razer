<?php
namespace System\File;


/**
 * Class File
 * Image upload and saving it to database
 */
class File
{
    public function __construct()
    {
    }

    /**
     * @param string $path The path where to upload the file
     * @param string $input_name The name of the input field
     * @return bool|string True if the file/image was uploaded, false if not and string in case of a user error
     */
    public function upload_image_in_bg($path, $input_name) {
        $root = $_SERVER['DOCUMENT_ROOT']."/";
        $target_dir = $root.$path."/";
        $target_file = $target_dir . basename($_FILES[$input_name]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES[$input_name]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
        }
        // Check if file already exists
        if (file_exists($target_file)) {
            $uploadOk = 0;
            return "Sorry, file already exists.";
        }
        // Check file size
        if ($_FILES[$input_name]["size"] > 500000) {
            $uploadOk = 0;
            return "Sorry, your file is too large.";
        }
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
            $uploadOk = 0;
            return "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            return "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES[$input_name]["tmp_name"], $target_file)) {
                return true;
            } else {
                return false;
            }
        }

    }

    /**
     * @param string $input_name The name of the input file
     * use it to save the image reference in the database
     * @return mixed
     */
    public function get_image_name($input_name) {
        return $_FILES[$input_name]["name"];
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
    public function uploadFilesToServer(string $dir, string $input_name)
    {
            // File upload configuration 
            $root = $_SERVER['DOCUMENT_ROOT']."/";

            $targetDir = $root . $dir ."/"; 
            $allowTypes = array('jpg','png','jpeg','gif', 'pdf', 'docx', 'ppt'); 
            
            $statusCode = 5; 
            $fileNames = array_filter($_FILES[$input_name]['name']); 
            if(!empty($fileNames)){ 
                foreach($_FILES[$input_name]['name'] as $key=>$val){ 
                    // File upload path 
                    $fileName = basename($_FILES[$input_name]['name'][$key]); 
                    $targetFilePath = $targetDir . $fileName; 
                    
                    // Check whether file type is valid 
                    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION)); 
                    
                    if(in_array($fileType, $allowTypes)){ 
                        // Upload file to server 
                        if(move_uploaded_file($_FILES[$input_name]["tmp_name"][$key], $targetFilePath)){ 
                            // Image db insert sql 
                            $statusCode = 1;
                        }else{ 
                            $statusCode = 0;
                        } 
                    }else{ 
                        $statusCode = 2; 
                    } 
                } 

            }else{ 
                $statusCode = 3; 
            } 
        return $statusCode;
    }


    /**
     * Get an array of uploaded file names for inserting in the database
     *
     * @param string $column_name The column name corresponding files
     * @param string $input_name The name of the input that submitted
     * @return array
     * 
     * A associative array returned is form of array(id => id_no, column_name => file_name)
     * 
     * The order of the files is returned as you selected them
     * 
     * Loop through the array to get the data
     */
    public function getFilesForDatabaseStorage(string $column_name, string $input_name) {
        $fileNames = array_filter($_FILES[$input_name]['name']);

        $filesArray = array();

        if(!empty($fileNames)){ 
            $i = 0;
            foreach($_FILES[$input_name]['name'] as $key=>$val){ 
                // File upload path 
                $i++;

                $fileName = basename($_FILES[$input_name]['name'][$key]);
                $filesArray[] = array(
                    "id" => $i,
                    $column_name => $fileName
                );
            }
        }
        asort($filesArray);
        return $filesArray;
    }
}
