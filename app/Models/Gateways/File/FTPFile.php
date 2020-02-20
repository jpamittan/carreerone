<?php
namespace App\Models\Gateways\File;

use App\Models\Gateways\ExternalFileGateway;
use App\Models\Proxies\FileProxy;

/**
 * External File Gateway Implmentation for FTP
 */
class FTPFile implements ExternalFileGateway
{

    /**
     * Placeholder for sftp config
     */
    protected $config;

    /**
     * Placeholder for sftp conneciton id
     */
    protected $conn_id;

    /**
     * Open a file connection
     * @param array $config
     * @return boolean
     */
    public function open($config)
    {

        $this->config = $config;
        $this->conn_id = ftp_connect($this->config["server"]);
        $login = ftp_login($this->conn_id, $this->config["username"], $this->config["password"]);
        ftp_pasv($this->conn_id, true);
        return $login;
    }

    /**
     * Get list of file is remote directory
     * @param string $directory
     * @param array $options
     * @return array
     */
    public function getFileList($directory, $options = array())
    {
        $file_list = ftp_nlist($this->conn_id, $directory);
        if ($file_list === false) {
            return array();
        }
        return $file_list;
    }

    /**
     * Upload file to destination from source
     * @param FileProxy $source
     * @param string $destination
     * @param array $options
     * @return bool
     */
    public function upload($source, $destination, $options = array())
    {

        if (array_key_exists('mode', $options)) {
            $mode = ($options['mode'] == 'binary') ? FTP_BINARY : FTP_ASCII;
        } else {
            $mode = FTP_ASCII;
        }

        // $tempFile = false;
        // if (empty($source)) {
        //     $tempFile = true;
        //     $source->writeContentsToFile();
        // }
// print_R($destination);exit;
        $return = ftp_put($this->conn_id, $destination, $source, FTP_BINARY);
        
        
        return $return;
    }

    public function uploadXml($source, $destination, $path, $options = array())
    {
        $rename = basename($destination, '.bak');

        if (array_key_exists('mode', $options)) {
            $mode = ($options['mode'] == 'binary') ? FTP_BINARY : FTP_ASCII;
        } else {
            $mode = FTP_ASCII;
        }

        $put = ftp_put($this->conn_id, $destination, $source, $mode);

        if($put){
            $return = ftp_rename($this->conn_id, $destination, $path.'/'.$rename);
        }
        
        unlink($source);

        return $return;
    }

    /**
     * Download file from source to destination
     * @param string $source
     * @param string $destination
     * @param array $options
     * @return FileProxy
     */
    public function download($source, $destination, $options = array())
    {
        
        if (array_key_exists('mode', $options)) {
            $mode = ($options['mode'] == 'binary') ? FTP_BINARY : FTP_ASCII;
        } else {
            $mode = FTP_ASCII;
        }

         
       $file =  ftp_get($this->conn_id, $destination, $source, FTP_BINARY);
      
        return  $file;
    }

    /**
     * Delete passed in file from underlying storage
     * @param string $file
     * @param array $options
     * @return bool
     */
    public function delete($file,   $options = array())
    {
        if (ftp_delete($this->conn_id, $file))
            {
            return true;
            }
            else
            {
           return false;
        }
    }

    /**
     * Delete a remote directory
     * @param string $file
     * @param Array $files optional field. If provided, only removes these files
     * @param array $options
     * @return bool
     */
    public function removeDirectory($directory, array $files = array(), $options = array())
    {
        //TODO: Need to implement
        return false;
    }

    /**
     * Close a file connection
     */
    public function close()
    {
        ftp_close($this->conn_id);
    }

    public function getConnId(){
        return $this->conn_id;
    }
    
    /**
     * Upload file to destination from source
     * @param string $source
     * @param string $destination
     * @param array $options
     * @return bool
     */
    public function upload_binary($source, $destination, $options = array())
    {
        // upload a file
        $status = ftp_put($this->conn_id, $destination, $source, FTP_BINARY);
    
        return $status;
    }
}
