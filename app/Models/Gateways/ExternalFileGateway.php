<?php
namespace App\Models\Gateways;

interface ExternalFileGateway
{
    /**
     * Open a file connection
     * @param array $config
     */
    public function open($config);

    /**
     * Get list of file is remote directory
     * @param string $directory
     * @param array $options
     * @return array
     */
    public function getFileList($directory, $options = array());

    /**
     * Get list of file is remote directory
     * @param FileProxy $source
     * @param string $destination
     * @param array $options
     * @return bool
     */
    public function upload($source, $destination, $options = array());

    /**
     * Get list of file is remote directory
     * @param string $source
     * @param string $destination
     * @param array $options
     * @return FileProxy
     */
    public function download($source, $destination, $options = array(),$name);

    /**
     * Get list of file is remote directory
     * @param string $file
     * @param array $options
     * @return bool
     */
    public function delete($file,  $options = array());

    /**
     * Delete a remote directory
     * @param string $file
     * @param Array $files optional field. If provided, only removes these files
     * @param array $options
     * @return bool
     */
    public function removeDirectory($directory, array $files = array(), $options = array());

    /**
     * Close a file connection
     */
    public function close();

}
