<?php

namespace App\Http\Controllers\site;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\site\AdminController;
use App\Models\Services\ReadCSVService;
use App\Models\Gateways\File\FTPFile;
use App\Models\Gateways\File\SFTPFile;
use App\Models\Services\JobAssetService;
use Carbon\Carbon;
use Maatwebsite\Excel\Excel;
use Config, View, Redirect, Storage;

class ReadCSVController extends AdminController {
  public function importCSV() {
    $username = Config::get('filesystems.disks.dropzone_download_featured.username');
    $password = Config::get('filesystems.disks.dropzone_download_featured.password');
    $server = Config::get('filesystems.disks.dropzone_download_featured.server');
    $path_to_download = Config::get('filesystems.disks.dropzone_download_featured.path_to_download');
    $config = array('username'=>$username,'password'=>$password,'server'=>$server);
    $service = new ReadCSVService();
    $ftp = new FTPFile();
    $login = $ftp->open($config);
    $lists = $ftp->getFileList($path_to_download);
    foreach($lists as $list) {
      $path_info = pathinfo($list);
      if (isset($path_info['extension']) && $path_info['extension'] == 'xlsx') {
        $file_proc_id = $service->postFileProcessingStatus($path_info["basename"],0);
        if (!file_exists(storage_path().'/temp/')) {
          mkdir(storage_path().'/temp/',0777,true);
        }
        $path = storage_path().'/temp/'.$path_info["basename"];
        $download_file = $ftp->download($list,$path);
        if ($download_file == 1) {
          $service->postFileProcessingStatusUpdate(
            $path_info["basename"],
            1,
            $file_proc_id
          );
          $remote_file = $path_to_download.'processing/'.$path_info["basename"];
          $move = $ftp->upload($path,$remote_file);
          $delete = $ftp->delete($list);
          $service->postFileProcessingStatusUpdate(
            $path_info["basename"],
            2,
            $file_proc_id
          );
          $excel = \App::make('excel');
          try {
            $service->postFileProcessingStatusUpdate(
              $path_info["basename"],
              3,
              $file_proc_id
            );
            $data = $excel->selectSheetsByIndex(0)->load($path)->get();
            foreach ($data->toArray() as $row) {
              $service->postCSV($row);
            }         
          } catch (Exception $e) {
            $service->postFileProcessingError(
              $path_info["basename"],
              2,
              $file_proc_id,
              $e
            );
          }
          $service->postFileProcessingStatusUpdate(
            $path_info["basename"],
            4,
            $file_proc_id
          );                    
        }
        $remote_file = $path_to_download.'processing/'.$path_info["basename"];
        $delete = $ftp->delete($remote_file);
        $archieve_path = $path_to_download.'archive/'.$path_info["basename"];
        $archieve = $ftp->upload($path,$archieve_path);
        if ($archieve) {
          $service->postFileProcessingStatusUpdate(
            $path_info["basename"],
            5,
            $file_proc_id
          );
        }
        $delete_local = Storage::delete($path);
        if ($delete_local) {
          $service->postFileProcessingStatusUpdateFinal(
            $path_info["basename"],
            6,
            $file_proc_id
          );
        }
      }
    }  
  }
}
