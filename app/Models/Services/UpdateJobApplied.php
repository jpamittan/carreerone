<?php
namespace App\Models\Services;

use App\Models\Crm\CrmConnector;
use Illuminate\Support\Facades\Config;
use DB;

class UpdateJobApplied extends CrmConnectorService {
	public function __construct() {
	    parent::__construct();
	}

	public function updateCRMJobApplied($id,$status,$resume_id=null,$cover_letter_id=null,$supporting_docs_id=null) {
		$comments='';
		$resume = DB::table('ins_cv')->where('id','=',$resume_id)->first() ;
        $ins_covering_letter = DB::table('ins_covering_letter')->where('id','=',$cover_letter_id)->first();
        $ins_supporting_doc = DB::table('ins_supporting_doc')->where('id','=',$supporting_docs_id)->first();
        if(!empty($resume)) {
            $datail['resumelink'] = $resume->resume_url;
            $datail['ins_covering_letter'] ='';
            $datail['ins_supporting_doc'] ='';
            if(!empty($ins_covering_letter)) {
                $datail['ins_covering_letter'] = $ins_covering_letter->coveringletter_url;
            }
            if(!empty($ins_supporting_doc)) {
                $datail['ins_supporting_doc'] = $ins_supporting_doc->url;
            }
            $comments = '';
            $comments .=' Resume link :' . $datail['resumelink'] . "\r\n" ;
            $comments .=' CoverLetter link :' . $datail['ins_covering_letter']. "\r\n" ;
            $comments .=' Supporting Doc link :' . $datail['ins_supporting_doc'] . "\r\n";
      	}
		$fields = [
			['name'=>'ins_progress', 'value' => $status, 'type'=>'option'],
			['name'=>'new_comment', 'value' => $comments, 'type'=>'string'],
		];
        $result =  $this->getCrmConnector()->getController()->updateEntity('new_jobapplied',$id, $fields);
		return $result;
	}

	public function updateCRMJobApplied_new_progress($id,$status) {
		$connector = new CrmConnector($this->crm_url, $this->username, $this->password);
		$controller = $connector->getController();
		$fields = [
			['name'=>'new_progress', 'value' => $status, 'type'=>'option'],
		];
		$result = $this->getCrmConnector()->getController()->updateEntity('new_jobapplied',$id, $fields);
		return $result;
	}

	public function updateCRMJobAppliedINSProgress($id,$status) {
		$connector = new CrmConnector($this->crm_url, $this->username, $this->password);
		$controller = $connector->getController();
		$fields = [
			['name'=>'ins_progress', 'value' => $status, 'type'=>'option'],

		];
        $result =  $this->getCrmConnector()->getController()->updateEntity('new_jobapplied',$id, $fields);
		return $result;
	}
}
