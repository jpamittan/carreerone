<?php
namespace App\Models\Gateways\File;

use App\Models\Gateways\ExternalFileGateway;
use App\Models\Proxies\FileProxy;
use Aws\S3\S3Client;

/**
 * External File Gateway Implmentation for S3
 */
class S3File implements ExternalFileGateway
{

    /**
     * Placeholder for s3 config
     * @var array
     */
    protected $config;

    /**
     * Placeholder for s3client handle
     * @var [type]
     */
    protected $s3_client;

    /**
     * Open a file connection
     * @param array $config
     * @return boolean
     */
    public function open($config)
    {
        $this->config = $config;
        if (empty($this->config['bucket'])) {
            $this->config['bucket'] = $this->config['buckets']['default'];
        }
        $this->s3_client = S3Client::factory($this->config);
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
        $arr=array();
        $objects = $this->s3_client->listObjects(array('Bucket' => $this->config['bucket'], 'MaxKeys' => 1000 ));
        $files = $objects->getPath('Contents');
        foreach ($files as $file) {
            $filename = $file['Key'];
            $arr[] = $filename;

        }
        return $arr;
    }

    public function getFileByName($filename_input, $options = array())
    {
        // Get the object
       
        return   $this->s3_client->doesObjectExist($this->config['bucket'] , $filename_input );
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
       
        $parameters = array(
            'Bucket' => $this->config['bucket'],
            'Key' => $destination,
            'Body' => $source->getContents()
        );

        // add content type if passed in
       // $parameters['ContentType'] = $source->mime_type;

        // store
        $result = $this->s3_client->putObject($parameters);

        if (isset($options['object'])) {
            return $result;
        }

        $request_id = $result->get('RequestId');
        return !empty($request_id);
    }
    
    /**
     * Download file from source to destination
     * @param string $source
     * @param string $destination
     * @param array $options
     * @return FileProxy
     */
    
    public function download($source, $destination, $options = array(),$name)
    {
        $path ='';
        if(!empty($options) && isset($options['path'])){
            $path = $options['path'];
        }
        $fname = $this->getFileByName($path. $source);
        if($fname){
            $result = $this->s3_client->getObject(array(
                'Bucket' => $this->config['bucket'],
                'Key' => $path. $source,
                'ResponseContentDisposition' => 'attachment',
            ));
            $proxy = null;
            if ($result) {
                $body = $result['Body'];
                $content_type = $result['ContentType'];

                if (!empty($destination)) {
                    $proxy = new FileProxy($body->__toString(), 'data', array(
                        'content_type' => $content_type,
                        'write_file' => true,
                        'destination' => $destination,
                        'name' =>   $name,
                    ));
                } else {
                    $proxy = new FileProxy($body->__toString(), 'data', array(
                        'content_type' => $content_type,
                    ));
                }
            }
            return $proxy;
        }
        return false;
    }
    /**
     * Delete passed in file from underlying storage
     * @param string $file
     * @param array $options
     * @return bool
     */
    public function delete($file, $options = array())
    {
        $parameters = array(
            'Bucket' => $this->config['bucket'],
            'Key' => $file,
        );

        // delete object
        $result = $this->s3_client->deleteObject($parameters);
        $request_id = $result->get('RequestId');
        return !empty($request_id);
    }

    /**
     * Function to remove directory recursively.
     * Delete folders only if no file list is given
     * @param string $srouce_dir
     * @param Array $files optional field. If provided, only removes these files
     * @return void
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

    }
}
