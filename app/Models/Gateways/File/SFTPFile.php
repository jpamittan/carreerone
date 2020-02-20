<?php
namespace App\Models\Gateways\File;

use App\Models\Gateways\ExternalFileGateway;
use Net_SFTP;

/**
 * External File Gateway Implmentation for SFTP
 */
class SFTPFile implements ExternalFileGateway
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
        $login = false;
        $this->conn_id = new \Net_SFTP($config["server"]);

        if (array_key_exists('key', $config)) {
            // TODO:
            $login = true;

        } elseif (array_key_exists('password', $config)) {
            $login = $this->conn_id->login($this->config["username"], $this->config["password"]);
        }
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
        $file_list = $this->conn_id->nlist($directory);
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
        //TODO: Implement
        return false;
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
        $this->conn_id->get($source, $destination);
        return new FileProxy($destination, 'file');
    }

    /**
     * Delete passed in file from underlying storage
     * @param string $file
     * @param array $options
     * @return bool
     */
    public function delete($file, $options = array())
    {
        //TODO: Implement
        return false;
    }

    /**
     * Delete a remote directory
     * @param string $file
     * @param Array $files optional field. If provided, only removes these files
     * @param array $options
     * @return bool
     */
    public function removeDirectory($source_dir, array $files = array(), $options = array())
    {
        //TODO: Need to implement
        return false;
    }

    /**
     * Close a file connection
     */
    public function close()
    {
        $this->conn_id = false;
    }
}
