<?php

namespace App\Models\Repositories;

use App\Models\Crm\CrmConnector;
use App\Models\Crm\Traits\CrmConnectorTrait;
use App\Models\Entities\Client;
use App\Models\Entities\User;
use App\Models\Services\EmailService;
use App\Models\Entities\Employee;
use DB, Config;

class UserSkillPushRepository extends RepositoryBase {
    use CrmConnectorTrait;
    private $emailService;

    public function updateEmployeesSkill($id) {
        $ins_skill_q = DB::table('ins_user_skill_assesment')
            ->select('ins_user_skill_assesment.id as id',
                'users.crm_user_id as ins_eitid',
                'ins_user_skill_assesment.frequency_id as ins_howfrequent',
                'ins_user_skill_assesment.recency_id as ins_howrecent',
                'ins_user_skill_assesment.level_id as ins_level',
                'ins_skill_id as ins_skill',
                'ins_job_category_id as new_occupationalcategory',
                'comment as ins_comment',
                'ins_user_skill_assesment.ins_skillsummary_id as ins_skillsummary_id')
            ->join('ins_skill_assement_types', 'ins_user_skill_assesment.skill_asse_type_id', '=', 'ins_skill_assement_types.id')
            ->join('ins_job_category', 'ins_skill_assement_types.job_category_id', '=', 'ins_job_category.id')
            ->join('users', 'ins_user_skill_assesment.candidate_id', '=', 'users.id')
            ->where('ins_user_skill_assesment.id', '=', $id)
            ->whereNotNull('ins_skillsummary_id')
            ->first();

        if (!empty($ins_skill_q)) {
            $ins_eitid = $ins_skill_q->ins_eitid;
            $ins_howfrequent = $ins_skill_q->ins_howfrequent;
            $ins_howrecent = $ins_skill_q->ins_howrecent;
            $ins_level = $ins_skill_q->ins_level;
            $ins_skill = $ins_skill_q->ins_skill;
            $new_occupationalcategory = $ins_skill_q->new_occupationalcategory;
            $ins_comment = $ins_skill_q->ins_comment;
            $ins_skillsummary_id = $ins_skill_q->ins_skillsummary_id;
            $fields = [
                ['name' => 'ins_eitid', 'value' => $ins_eitid, 'type' => 'entity', 'entity_name' => 'new_employee'],
                ['name' => 'ins_howfrequent', 'value' => $ins_howfrequent, 'type' => 'option'],
                ['name' => 'ins_howrecent', 'value' => $ins_howrecent, 'type' => 'option'],
                ['name' => 'ins_level', 'value' => $ins_level, 'type' => 'option'],
                ['name' => 'ins_skill', 'value' => $ins_skill, 'type' => 'entity', 'entity_name' => 'ins_skill'],
                ['name' => 'new_occupationalcategory', 'value' => $new_occupationalcategory, 'type' => 'entity', 'entity_name' => 'new_jobcategory'],
                ['name' => 'ins_comment', 'value' => $ins_comment, 'type' => 'string'],
            ];
            $result = $this->getCrmConnector()->getController()->updateEntity('ins_skillsummary', $ins_skillsummary_id, $fields);
        }
    }

    public function createEmployeesSkill($id) {
        $ins_skill_q = DB::table('ins_user_skill_assesment')
            ->select('ins_user_skill_assesment.id as id',
                'users.crm_user_id as ins_eitid',
                'ins_user_skill_assesment.frequency_id as ins_howfrequent',
                'ins_user_skill_assesment.recency_id as ins_howrecent',
                'ins_user_skill_assesment.level_id as ins_level',
                'ins_skill_id as ins_skill',
                'ins_job_category_id as new_occupationalcategory',
                'comment as ins_comment')
            ->join('ins_skill_assement_types', 'ins_user_skill_assesment.skill_asse_type_id', '=', 'ins_skill_assement_types.id')
            ->join('ins_job_category', 'ins_skill_assement_types.job_category_id', '=', 'ins_job_category.id')
            ->join('users', 'ins_user_skill_assesment.candidate_id', '=', 'users.id')
            ->where('ins_user_skill_assesment.id', '=', $id)
            ->whereNull('ins_skillsummary_id')
            ->first();
        if (!empty($ins_skill_q)) {
            $ins_eitid = $ins_skill_q->ins_eitid;
            $ins_howfrequent = $ins_skill_q->ins_howfrequent;
            $ins_howrecent = $ins_skill_q->ins_howrecent;
            $ins_level = $ins_skill_q->ins_level;
            $ins_skill = $ins_skill_q->ins_skill;
            $new_occupationalcategory = $ins_skill_q->new_occupationalcategory;
            $ins_comment = $ins_skill_q->ins_comment;
            $fields = [
                ['name' => 'ins_eitid', 'value' => $ins_eitid, 'type' => 'entity', 'entity_name' => 'new_employee'],
                ['name' => 'ins_howfrequent', 'value' => $ins_howfrequent, 'type' => 'option'],
                ['name' => 'ins_howrecent', 'value' => $ins_howrecent, 'type' => 'option'],
                ['name' => 'ins_level', 'value' => $ins_level, 'type' => 'option'],
                ['name' => 'ins_skill', 'value' => $ins_skill, 'type' => 'entity', 'entity_name' => 'ins_skill'],
                ['name' => 'new_occupationalcategory', 'value' => $new_occupationalcategory, 'type' => 'entity', 'entity_name' => 'new_jobcategory'],
                ['name' => 'ins_comment', 'value' => $ins_comment, 'type' => 'string'],
            ];
            $result = $this->getCrmConnector()->getController()->createEntity('ins_skillsummary', $fields);
            if (isset($result['id'])) {
                DB::table('ins_user_skill_assesment')
                    ->where('id', '=', $id)
                    ->update(['ins_skillsummary_id' => $result['id']]);
            }
        }
    }

    public function deleteEmployeesSkill($id) {
        $ins_skill_q = DB::table('ins_user_skill_assesment')
            ->select('ins_user_skill_assesment.id as id',
                'users.crm_user_id as ins_eitid',
                'ins_user_skill_assesment.frequency_id as ins_howfrequent',
                'ins_user_skill_assesment.recency_id as ins_howrecent',
                'ins_user_skill_assesment.level_id as ins_level',
                'ins_skill_id as ins_skill',
                'ins_job_category_id as new_occupationalcategory',
                'comment as ins_comment',
                'ins_user_skill_assesment.ins_skillsummary_id as ins_skillsummary_id')
            ->join('ins_skill_assement_types', 'ins_user_skill_assesment.skill_asse_type_id', '=', 'ins_skill_assement_types.id')
            ->join('ins_job_category', 'ins_skill_assement_types.job_category_id', '=', 'ins_job_category.id')
            ->join('users', 'ins_user_skill_assesment.candidate_id', '=', 'users.id')
            ->where('ins_user_skill_assesment.id', '=', $id)
            ->whereNotNull('ins_skillsummary_id')
            ->first();
        if (!empty($ins_skill_q)) {
            $ins_skillsummary_id = $ins_skill_q->ins_skillsummary_id;
            $result = $this->getCrmConnector()->getController()->deleteEntity('ins_skillsummary', $ins_skillsummary_id);
            if (isset($result['success'])) {
                echo "Problem deleting skill/ or skill does not exist in Dynamics\n";
            } else {
                print_r($result);
            }
        }
    }
}
