<?php
namespace App\Models\Gateways\ExternalFile;

use App\Models\Gateways\ExternalFileGateway;
use App\Models\Proxies\FileProxy;

/**
 * External File Gateway Implmentation for FileSystem
 */
class LocalFile implements ExternalFileGateway
{

    /**
     * Placeholder for local file config
     */
    protected $config;

    /**
     * Open a file connection
     * @param array $config
     * @return boolean
     */
    public function open($config)
    {
        $this->config = $config;
        return true;
    }

    /**
     * Get list of file in directory
     * @param string $directory
     * @param array $options
     * @return array
     */
    public function getFileList($directory, $options = array())
    {
        $file_array = array();

        //cleaning up the $directory with a trailing slash ('/')
        $directory = rtrim($directory, '/') . '/';
        $items = array_diff(scandir($directory), array('.', '..'));
        return $items;
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
        $content = null;
        if (!empty($destination)) {
            copy($source, $destination);
        }
        $proxy = new FileProxy($destination, 'file');
        return $proxy;
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
     * Function to remove directory recursively.
     * Delete folders only if no file list is given
     * @param string $srouce_dir
     * @param Array $files optional field. If provided, only removes these files
     * @return void
     */
    public function removeDirectory($source_dir, array $files = array(), $options = array())
    {
        $only_files = false;

        //clean up source_dir path with the trailing slash(/)
        $source_dir = rtrim($source_dir, '/');

        if (is_dir($source_dir)) {
            if (empty($files)) {
                $files = array_diff(scandir($source_dir), array('.', '..'));
                $only_files = false;
            } else {
                $only_files = true;
            }

            if (is_dir($source_dir)) {
                foreach ($files as $file) {
                    if (filetype($source_dir . '/' . $file) == 'dir') {
                        $this->removeDirectory($source_dir . '/' . $file);
                    } else {
                        unlink($source_dir . '/' . $file);
                    }
                }
                reset($files);
                if ($only_files == false) {
                    if ($source_dir != rtrim(storage_path('data/'), '/')) {
                        rmdir($source_dir);
                    }
                }
            } else {
                unlink($source_dir);
            }
        }
    }

    /**
     * Close a file connection
     */
    public function close()
    {

    }
}
