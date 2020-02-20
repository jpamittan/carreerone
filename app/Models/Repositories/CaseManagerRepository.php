<?php

namespace App\Models\Repositories;

use App\Models\Entities\CaseManager;
use App\Models\Entities\User;
use App\Models\Entities\RoleUser;
use App\Models\Gateways\Email\AWSEmail;
use App\Models\Repositories\EmailVerifiedRepository;
use DB, View, Config;

class CaseManagerRepository extends RepositoryBase {
    private $user;
    private $role;
    private $emailVerifiedRepository;

    public function importcasemangers($casemanagers) {
        foreach ($casemanagers as $manager) {
            $cheuser = DB::table('ins_system_users')->where('systemuserid', '=', $manager['systemuserid'])->select('ins_system_users.*')->first();
            if (empty($cheuser)) {
                $results = $this->casemanager($manager);
            } else {
                $results = $this->updatecasemanager($cheuser, $manager);
            }
            if ($results) {
                $fullName = !empty($manager['firstname']) ? $manager['firstname'] : '';
                $fullName .= (!empty($manager['lastname']) ? " " . $manager['lastname'] : '');
                app('App\Models\Services\CaseManagerService')->registerOrUpdateCaseManager($manager['systemuserid'], $manager['internalemailaddress'], trim($fullName));
                $this->roleUser = new RoleUser();
                $this->user = new User;
                $this->emailVerifiedRepository = new EmailVerifiedRepository();
                $results = $this->user->where('email', '=', $manager['internalemailaddress'])->first();
                if (empty($results)) {
                    $this->user->email = $manager['internalemailaddress'];
                    $this->user->first_name = !empty($manager['firstname']) ? $manager['firstname'] : '';
                    $this->user->last_name = !empty($manager['lastname']) ? $manager['lastname'] : '';
                    $this->user->is_active = 1;
                    $this->user->crm_user_id = $manager['systemuserid'];
                    $this->user->save();
                    $this->roleUser->user_id = $this->user->id;
                    $this->roleUser->role_id = 2;
                    $this->roleUser->save();
                    $token = date('U', strtotime('+1 day'));
                    $arr_return = array(
                        'returns' => true,
                        'id' => $this->user->id,
                        'email' => $this->user->email,
                        'name' => $this->user->first_name . ' ' . $this->user->last_name,
                        'expire_in' => date('d/m/Y h:i:s A', $token),
                        'token' => str_replace('=', '', base64_encode($token . '-' . $this->user->email)),
                    );
                    $save = $this->emailVerifiedRepository->savePasswordResets($arr_return);
                } else {
                    $results->email = $manager['internalemailaddress'];
                    $results->first_name = !empty($manager['firstname']) ? $manager['firstname'] : '';
                    $results->is_active = 1;
                    $results->crm_user_id = $manager['systemuserid'];

                    $results->save();
                }
            }
        }
    }

    public function casemanager($manager) {
        if (!empty($manager['internalemailaddress'])) {
            $case_manager = new CaseManager();
            $case_manager->systemuserid = $manager['systemuserid'];
            $case_manager->fullname = !empty($manager['fullname']) ? $manager['fullname'] : '';
            $case_manager->internalemailaddress = $manager['internalemailaddress'];
            $case_manager->createdon = $manager['createdon'];
            $case_manager->organizationid = !empty($manager['organizationid']) ? $manager['organizationid'] : '';
            $case_manager->phone = !empty($manager['address1_telephone1']) ? $manager['address1_telephone1'] : '';
            $save = $case_manager->save();
            return $save;
        }
        return false;
    }

    public function updatecasemanager($cheuser, $manager) {
        $update_manager = CaseManager::find($cheuser->id);
        $update_manager->fullname = !empty($manager['fullname']) ? $manager['fullname'] : '';
        $update_manager->internalemailaddress = $manager['internalemailaddress'];
        $update_manager->organizationid = !empty($manager['organizationid']) ? $manager['organizationid'] : '';
        $update_manager->phone = !empty($manager['address1_telephone1']) ? $manager['address1_telephone1'] : '';
        $update = $update_manager->save();
        return $update;
    }
}
