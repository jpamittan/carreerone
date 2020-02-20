<?php

namespace App\Models\Proxies;

use Format;
use Guzzle\Service\Client;
use Guzzle\Stream\PhpStreamRequestFactory;

class FileProxy {
    /**
     * Validation options
     */
    const FILE_UPLOAD_TYPE = 'type';
    const FILE_UPLOAD_SIZE = 'size';

    /**
     * Mapping of popular mime types vs extensions - used to get extension based on mimetype if required
     * @var mixed
     */
    protected $mime_type_extensions = array(
        'application/pdf' => 'pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/msword' => 'doc',
        'text/plain' => 'txt',
        'text/rtf' => 'rtf',
        'text/xml' => 'xml',
        'text/html' => 'html',
    );

    /**
     * List of allowed file upload types
     * @var array
     */
    protected $allowed_upload_types = array(
        'application/pdf' => 'pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/msword' => 'doc',
        'text/plain' => 'txt',
        'text/rtf' => 'rtf',
    );

    /**
     * Maximum file size allowed for the uploads - bytes
     * @var integer
     */
    protected $allowed_upload_size = 512000;

    /**
     * File Attributes
     */
    protected $attributes = array(
        'name' => null,
        'size' => 0,
        'path' => null,
        'extension' => null,
        'mime_type' => null,
        'remote' => 0,
        'valid' => false,
        'content' => null,
    );

    /**
     * Options passed in to the constructor that controls the behaviour of the proxy
     * @var array
     */
    protected $options = array(
        'content_type' => null,
        'write_file' => false,
        'destination' => null,
        'name' => null,
    );

    /**
     * Constructor function to create a proxy object
     * @param mixed $resource
     * @param string $source
     * @param array $options
     * @return void
     */
    public function __construct($resource, $source, $options = array()) {
        // assign valid options
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $this->options)) {
                $this->options[$key] = $value;
            }
        }

        // action based on source
        switch ($source) {
            // expects an Symfony\Component\HttpFoundation\File\UploadedFile object
            case 'desktop':
                $this->loadFromDesktop($resource);
                break;
            // expects an stdClass object that dropbox api is providing
            case 'dropbox':
                $this->loadFromDropbox($resource);
                break;
            // expects an stdClass object that google drive api is providing
            case 'gdrive':
                $this->loadFromGDrive($resource);
                break;
            // expects an stdClass object that career one api is provding
            case 'careerone':
                $this->loadFromCareerOne($resource);
                break;
            // expects a filename string
            case 'file':
                $this->loadFromFile($resource);
                break;
            // expects chunk of data to write to file
            case 'data':
                $this->loadFromData($resource);
                break;
            default:
                break;
        }
    }

    /**
     * Getter function to get a particular attribute of the file
     * @param string $key
     * @return string
     */
    public function __get($key) {
        $value = array_get($this->attributes, $key, null);
        return $value;
    }

    /**
     * Setter function to get a particular attribute of the file
     * @param string $key
     * @return string
     */
    public function __set($name, $value) {
        if (array_key_exists($name, $this->attributes)) {
            $this->attributes[$name] = $value;
            return true;
        }
        return false;
    }

    /**
     * Magic method implentation to cater empty functions issue
     * @param string
     * @return boolean
     */
    public function __isset($key) {
        return (bool) $this->{$key};
    }

    /**
     * Validates size of the enclosed file for upload
     * @return boolean
     */
    public function validate($mode) {
        switch ($mode) {
            case self::FILE_UPLOAD_TYPE:
                // if mime type or extension is missing, then try to load it
                if (empty($this->mime_type) or empty($this->extension)) {
                    $this->findMimeType();
                    $this->findExtension();
                }

                // check first on mime type else extension
                $valid = true;
                if (isset($this->mime_type)) {
                    $allowed_mime_types = array_keys($this->allowed_upload_types);
                    $valid = in_array($this->mime_type, $allowed_mime_types);
                } else if (isset($this->extension)) {
                    $allowed_extensions = array_values($this->allowed_upload_types);
                    $valid = in_array($this->extension, $allowed_extensions);
                }
                $this->valid = (bool) $valid;
                break;

            case self::FILE_UPLOAD_SIZE:
                if (!empty($this->path) && empty($this->size)) {
                    $this->size = filesize($this->path);
                }

                $valid = true;
                if ($this->size > $this->allowed_upload_size) {
                    $valid = false;
                }
                $this->valid = (bool) $valid;
                break;
        }
    }

    /**
     * Getter function to get file info
     * @return mixed
     */
    public function getInfo() {
        return $this->attributes;
    }

    /**
     * Function to check whetehr file is valid
     * @return boolean
     */
    public function isValid() {
        return $this->valid;
    }

    /**
     * Function to unlink associated file from the server
     * @return boolean
     */
    public function unlink($with_reset = true) {
        $status = unlink($this->path);
        if ($status && $with_reset) {
            $this->reset();
        }
        return $status;
    }

    /**
     * Function to get contents of the file associated
     * @return string
     */
    public function getContents() {
        if (empty($this->path) || is_null($this->path)) {
            return (string) $this->content;
        } else {
            return (string) file_get_contents($this->path);
        }
    }

    /**
     * Function to write contens to file if proxy is an in memory one
     */
    public function writeContentsToFile() {
        // if file is already on the disck then just return path
        if (is_file($this->path) && is_readable($this->path)) {
            return;
        }
        // get the file storage path
        $destination = $this->getFileStoragePath();
        // write and return real path
        $file = $this->generateFile($destination);
        $this->path = $file->getRealPath();
        // find mime type - if empty
        $this->findMimeType();
        // find extension - if empty
        $this->findExtension();
    }

    /**
     * Private function to load file from desktop - expecting a File Object
     * @param $file
     * @return boolean
     */
    private function loadFromDesktop($file) {
        if (is_a($file, 'Symfony\Component\HttpFoundation\File\UploadedFile')) {
            $this->name = $file->getClientOriginalName();
            $this->size = $file->getSize();
            $this->path = $file->getRealPath();
            $this->extension = $file->getExtension();
            $this->mime_type = $file->getMimeType();
            $this->remote = 0;
            $this->valid = $file->isValid();
            $this->content = file_get_contents($file->getRealPath());
            if (empty($this->extension)) {
                $this->extension = pathinfo(basename($this->name), PATHINFO_EXTENSION);
            }
            // find mime type - if empty
            $this->findMimeType();
            // find extension - if empty
            $this->findExtension();
            // get the file storage path
            $destination = $this->getFileStoragePath();
            // move file to storage
            move_uploaded_file($this->path, $destination);
            // check whether the file is readable, if set the path to new one
            if (is_file($destination) && is_readable($destination)) {
                $this->path = $destination;
                return true;
            }
        }
        return false;
    }

    /**
     * Private function to load file from dropbox - expecting an Object
     * @param $file
     * @return boolean
     */
    private function loadFromDropbox($file) {
        $this->name = $file['resume_name'];
        $this->remote = 1;
        $parts = explode('.', $this->name);
        $this->extension = end($parts);
        // find mime type - if empty
        $this->findMimeType();
        // find extension - if empty
        $this->findExtension();
        // get the file storage path
        $destination = $this->getFileStoragePath();
        // download file from link and if success set the path to new
        $status = $this->downloadFile($file['resume_url'], $destination);
        if ($status) {
            $this->path = $destination;
            $this->valid = true;
            return true;
        }
        return false;
    }

    /**
     * Private function to load file from google drive - expecting an Object
     * @param $file
     * @return boolean
     */
    private function loadFromGDrive($file) {
        $this->name = $file['file-name'];
        $this->mime_type = 'application/pdf';
        if($file['mime_type']){
            $this->mime_type = $file['mime_type'];
        }
        $this->remote = 1;
        $this->extension = 'pdf';
        // get the file storage path
        $destination = $this->getFileStoragePath();
        // download file from link and if success set the path to new
        $status = $this->downloadFileGoogle($file['file-id'], $destination, $file['google-auth-token']);
        if ($status) {
            $this->path = $destination;
            $this->valid = true;
            return true;
        }
        return false;
    }

    /**
     * Private function to load normal file
     * @param string $filename
     * @return boolean
     */
    private function loadFromFile($filename) {
        if (is_file($filename) && is_readable($filename)) {
            $this->name = $filename;
            // wrap it in file info
            $file = new \SplFileInfo($filename);
            $this->size = $file->getSize();
            $this->path = $file->getRealPath();
            $this->extension = $file->getExtension();
            $this->remote = 0;
            $this->valid = true;
            // find mime type - if empty
            $this->findMimeType();
            // find extension - if empty
            $this->findExtension();
            return true;
        }
        return false;
    }

    /**
     * Private function to load file from memory - expecting a string
     * @param mixed $input chunk of data
     * @return boolean
     */
    private function loadFromData($input) {
        $this->content = $input;
        if (!is_null($this->content)) {
            $write_file = $this->options['write_file'];
            $this->mime_type = $this->options['content_type'];
            $this->name = $this->options['name'];
            if ($write_file) {
                // find mime type - if empty
                $this->findMimeType();
                // find extension - if empty
                $this->findExtension();
                // get the file storage path
                $destination = $this->getFileStoragePath();
                // write file to disk
                $file = $this->generateFile($destination);
                $this->name = $file->getFileName();
                $this->size = $file->getSize();
                $this->path = $file->getRealPath();
                $this->remote = 0;
                $this->valid = false;
            } else {
                $this->size = strlen($this->content);
                $this->path = null;
                $this->remote = 0;
                $this->valid = true;
                // find mime type - if empty
                $this->findMimeType();
                // find extension - if empty
                $this->findExtension();
            }
            // check whether the file is readable, if set the path to new one
            if (is_file($this->path) && is_readable($this->path)) {
                $this->valid = true;
                return true;
            }
        }
        return false;
    }

    /**
     * Private function to load file from Career One - expecting an Object
     * @param $file
     * @return boolean
     */
    private function loadFromCareerOne($file) {
        $token = \ConfigProxy::get('careerone.profile_token');
        $this->name = $file['file-name'];
        // get the file storage path
        $this->path = $this->getFileStoragePath() . '.' . $this->extension;
        $this->remote = 1;
        $client = new Client();
        $file_link = $file['file-url'] . '&sToken=' . $token;
        $request = $client->get($file_link, array('stream' => true));
        $factory = new PhpStreamRequestFactory();
        $stream = $factory->fromRequest($request);
        $fh = fopen($this->path, 'w');
        $tmp_file = $this->path;
        $remainder = '';
        $state = 'start';
        $extension = '';
        while (($read = $stream->read(1000))) {
            $content = '';
            $data = $remainder . $read;
            $remainder = '';
            switch ($state) {
                case 'start':
                    // Consider the state as start until we find the opening of the
                    // first array "[". Pass the remeninder of the current string to remainder and
                    // set state to data to allow the data logic to process it
                    if ($start = strpos($data, '[')) {
                        $remainder = substr($data, $start + 1);
                        $state = 'data';
                    } else {
                        // its not a file content
                        return false;
                    }
                    break;
                case 'data':
                    // Process data chunk by chunk, passing the remainder on until it gets
                    // to the end of the array "]", then pass the rest on to the end state
                    $end = strpos($data, ']');
                    if ($end > -1) {
                        $remainder = substr($data, $end + 1);
                        $data = substr($data, 0, $end);
                        $content = implode(array_map("chr", explode(',', $data)));
                        $state = 'end';
                    } else {
                        $last = strrpos($data, ',');
                        $remainder = substr($data, $last + 1);
                        $data = substr($data, 0, $last);
                        $content = implode(array_map("chr", explode(',', $data)));
                    }
                    break;
                case 'end':
                    // This should now contain the entire end of the file, as it will be
                    // the remainder of the last plus 100 fbsql_set_characterset(link_identifier, characterset)
                    $match = array();
                    preg_match('/"FileExt":"(.[a-z]*)"/', $data, $match);
                    $extension = ltrim($match[1], '.');
                    $this->extension = $extension;
                    $this->mime_type = array_flip($this->mime_type_extensions)[$extension];
                    $this->path = $this->path . $extension;
                    break;
            }
            fwrite($fh, $content);
        }
        // process if still reminder left at the 'end'
        if ($remainder != '') {
            $match = array();
            preg_match('/"FileExt":"(.[a-z]*)"/', $remainder, $match);
            $extension = ltrim($match[1], '.');
            $this->extension = $extension;
            $this->mime_type = array_flip($this->mime_type_extensions)[$extension];
            $this->path = $this->path . $extension;
        }
        // get all content from the temp file to load proxy
        $this->content = file_get_contents($tmp_file);
        fclose($fh);
        unlink($tmp_file);
        // find mime type - if empty
        $this->findMimeType();
        // find extension - if empty
        $this->findExtension();
        //generate the file
        $this->generateFile($this->path);
        // check whether the file is readable
        if (is_file($this->path) && is_readable($this->path)) {
            $this->valid = true;
            return true;
        }
        return false;
    }

    /**
     * Private function to generate file from contents
     * @param string $destination
     * @return File
     */
    private function generateFile($destination) {
        file_put_contents($destination, $this->content);
        $file = new \SplFileInfo($destination);
        return $file;
    }

    /**
     * Private function to download file from a link
     * @param string $link
     * @param string $destination
     * @return boolean
     */
    private function downloadFile($link, $destination) {
        $client = new Client();
        $request = $client->createRequest('GET', $link)
                          ->setResponseBody($destination);
        $response = $client->send($request);
        // check whether the file is readable
        if (is_file($destination) && is_readable($destination)) {
            return true;
        }
        return false;
    }

    /**
     * Private function to download file from a link
     * @param string $link
     * @param string $destination
     * @return boolean
     */
    private function downloadFileGoogle($link, $destination, $authorization) {
        $client = new Client();
        $client->setDefaultOption('headers', array('Authorization' => "Bearer " . $authorization));
        $request = $client->createRequest('GET', "https://www.googleapis.com/drive/v2/files/" . $link);
        $response = $client->send($request);
        $data = json_decode($response->getBody(true));
        if (isset($data->exportLinks) && isset($data->exportLinks->{'application/pdf'})) {
            $exportUrl = $data->exportLinks->{'application/pdf'};
        } elseif (isset($data->downloadUrl)) {
            $exportUrl = $data->downloadUrl;
        } else {
            throw new \Exception("Unable to download file");
        }
        $request = $client->createRequest('GET', $exportUrl)
                          ->setResponseBody($destination);
        $response = $client->send($request);
        // check whether the file is readable
        if (is_file($destination) && is_readable($destination)) {
            return true;
        }
        return false;
    }

    /**
     * Private function to reset file attributes
     * @param string $link
     * @param string $destination
     * @return boolean
     */
    private function reset() {
        $this->attributes = array(
            'name' => null,
            'size' => 0,
            'path' => null,
            'extension' => null,
            'mime_type' => null,
            'remote' => 0,
            'valid' => false,
            'content' => null,
        );
        $this->options = array(
            'content_type' => null,
            'write_file' => false,
            'destination' => null,
            'name' => null,
        );
    }

    /**
     * Private function to find mime type for the file
     * @param string
     * @return string
     */
    private function findMimeType() {
        $mime_type = $this->mime_type;
        $path = $this->path;
        if (empty($mime_type) && !empty($path)) {
            $fi = new \finfo(FILEINFO_MIME_TYPE);
            $this->mime_type = $fi->file($this->path);
        }
        // hack for msword files reporting as application/zip
        if ($this->mime_type == 'application/zip' && in_array($this->extension, $this->mime_type_extensions)) {
            $mime_types_by_extesion = array_flip($this->mime_type_extensions);
            $this->mime_type = $mime_types_by_extesion[$this->extension];
        }
        if (empty($this->mime_type) && !empty($this->extension)) {
            foreach ($this->mime_type_extensions as $key => $value) {
                if ($this->extension == $value) {
                    $this->mime_type = $key;
                    return;
                }
            }
        }
    }

    /**
     * Private function to find extension for the file - Make sure this is being called after mimetype find above
     * to make sure it gets the info just in case if extension is missing
     * @param string
     * @return string
     */
    private function findExtension() {
        $extension = $this->extension;
        // get extension using php inbuilt
        if (empty($extension)) {
            $extension = pathinfo($this->path, PATHINFO_EXTENSION);
        }
        // if still empty use mime type to find it
        if (empty($extension)) {
            $mime_type = $this->mime_type;
            if (!empty($mime_type)) {
                $extension = array_get($this->mime_type_extensions, $mime_type, null);
            }
        }
        // get extension using filename split
        if (empty($extension)) {
            $extension = pathinfo($this->name, PATHINFO_EXTENSION);
        }
        $this->extension = $extension;
    }

    /**
     * Private function to get the storage path for the file
     * @return string
     */
    private function getFileStoragePath() {
        $destination = $this->options['destination'];
        // set a default path to make sure, it always return something
        $path = storage_path() . '/data/' . time() . '_' . $this->name;
        if (!is_null($destination)) {
            $name_parts = preg_split('/\./', $destination);
            if (count($name_parts) > 1) {
                $path = $destination;
            } else {
                if (!is_dir($destination)) {
                    mkdir($destination, 0777, true);
                }
                $path = rtrim($destination, '/') . '/' . $this->name;
            }
        }
        //$path = Format::slugify($path);
        return $path;
    }

    public function decryptPGP() {
        return shell_exec("echo '" . $passphrase . "' | gpg --passphrase-fd 0 -o " . $file_out . " -d " . $file_in);
    }

    public function encryptPGP() {
        return shell_exec("echo '" . $passphrase . "' | gpg --passphrase-fd 0 -o " . $file_out . " -e " . $file_in);
    }
}
