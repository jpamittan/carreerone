<?php

namespace App\Models\Repositories;

use App\Models\Crm\CrmConnector;
use App\Models\Crm\Traits\CrmConnectorTrait;
use App\Models\Entities\Client;
use App\Models\Entities\User;
use App\Models\Services\EmailService;
use App\Models\Entities\Employee;
use DB, Config;

class UserPushRepository extends RepositoryBase {
    use CrmConnectorTrait;
    private $emailService;

    public function pushEmployees() {
        $employees = DB::table('users')->join('ins_employees', 'ins_employees.user_id', '=', 'users.id')->get();
        foreach ($employees as $ins_employee) {
            $fields = [
                ['name' => 'new_personalhomenumber', 'value' => $ins_employee->new_personalhomenumber, 'type' => 'string'],
                ['name' => 'new_personalmobilenumber', 'value' => $ins_employee->new_personalmobilenumber, 'type' => 'string'],
                ['name' => 'new_firstname', 'value' => $ins_employee->new_firstname, 'type' => 'string'],
                ['name' => 'new_surname', 'value' => $ins_employee->new_surname, 'type' => 'string'],

            ];
            $locations = DB::table('ins_user_job_locations')
            ->join('ins_locations', 'ins_user_job_locations.ins_location_id', '=', 'ins_locations.id')
            ->where('user_id', '=', $ins_employee->id)
            ->get();
            if (!empty($locations)) {

                $counter = 1;
                foreach ($locations as $loc) {
                    $fields[] = ['name' => 'new_jobmatchinglocation' . $counter, 'value' => $loc->ins_location_id, 'type' => 'entity', 'entity_name' => 'new_location'];
                    $counter++;
                }
            }
            $result = $this->getCrmConnector()->getController()->updateEntity('new_employee', $ins_employee->employeeid, $fields);
        }

    }

    public function pushEmployeesLocations($userID) {
        $ins_employee = DB::table('users')
            ->join('ins_employees', 'ins_employees.user_id', '=', 'users.id')->where('users.id', '=', $userID)->first();
        $locations = DB::table('ins_user_job_locations')
            ->join('ins_locations', 'ins_user_job_locations.ins_location_id', '=', 'ins_locations.id')
            ->where('user_id', '=', $ins_employee->user_id)//
            ->get();
        for ($counter = 1; $counter <= 4; $counter++) {
            $fields1[] = ['name' => 'new_jobmatchinglocation' . $counter, 'value' => "", 'type' => 'entity', 'entity_name' => 'new_location'];
        }
        $result = $this->getCrmConnector()->getController()->updateEntity('new_employee', $ins_employee->employeeid, $fields1);
        if (!empty($locations)) {
            $counter = 1;
            foreach ($locations as $loc) {
                $fields[] = ['name' => 'new_jobmatchinglocation' . $counter, 'value' => $loc->ins_location_id, 'type' => 'entity', 'entity_name' => 'new_location'];

                $counter++;
            }
        }
        if (!empty($fields)) {
            $result = $this->getCrmConnector()->getController()->updateEntity('new_employee', $ins_employee->employeeid, $fields);
        }
    }

    public function pushEmployeesCategories($userID) {
        $ins_employee = DB::table('users')
            ->join('ins_employees', 'ins_employees.user_id', '=', 'users.id')->where('users.id', '=', $userID)->first();
        $categories = DB::table('ins_user_job_category_types')
            ->join('ins_job_category', 'ins_user_job_category_types.job_category_type_id', '=', 'ins_job_category.id')
            ->where('user_id', '=', $ins_employee->user_id)
            ->where('pending', '=', 0)
            ->get();
        for ($counter = 1; $counter <= 8; $counter++) {
            $fields1[] = ['name' => 'ins_jobcategory' . $counter, 'value' => "", 'type' => 'entity', 'entity_name' => 'new_jobcategory'];
        }
        $result = $this->getCrmConnector()->getController()->updateEntity('new_employee', $ins_employee->employeeid, $fields1);
        if (!empty($categories)) {
            $counter = 1;
            foreach ($categories as $cat) {

                $fields[] = ['name' => 'ins_jobcategory' . $counter, 'value' => $cat->ins_job_category_id, 'type' => 'entity', 'entity_name' => 'new_jobcategory'];

                $counter++;
            }
        }
        if (!empty($fields)) {
            $result = $this->getCrmConnector()->getController()->updateEntity('new_employee', $ins_employee->employeeid, $fields);
        }
    }

    public function pushEmployeeIndividual($employee_id, $fields) {
        $this->getCrmConnector()->getController()->updateEntity('new_employee', $employee_id, $fields);
    }
}
