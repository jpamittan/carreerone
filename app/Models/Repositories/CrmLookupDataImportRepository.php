<?php

namespace App\Models\Repositories;

use App\Models\Entities\JobCategory;
use App\Models\Entities\JobCategoryType;
use App\Models\Entities\InsLocation;
use App\Models\Entities\InsSuburb;
use App\Models\Entities\AgencyDetails;
use App\Models\Entities\InsSkillAssementTypes;

class CrmLookupDataImportRepository extends RepositoryBase {
	public function importJobCategories($categories) {
		foreach ($categories as $ins_category) {
			if (
				isset($ins_category['statuscode']) && 
				$ins_category['statuscode']['bValue'] == 2
			) {
				continue;
			}
			$jobCategory = JobCategory::firstOrNew(['ins_job_category_id' => $ins_category['new_jobcategoryid']]);
			$jobCategory->category_name = array_get($ins_category, 'new_name');
			$jobCategory->ins_job_category_id = array_get($ins_category, 'new_jobcategoryid');
			if (isset($ins_category['new_jobcategorytypeid'])) {
				$jobCategoryType  = JobCategoryType::where('ins_job_category_type_id', $ins_category['new_jobcategorytypeid']['bId'])->first();
				$jobCategory->job_category_type_id = $jobCategoryType->id;
			}
			$jobCategory->is_active = 1;	
			$jobCategory->save();
		}
	}
	
	public function importJobCategoryTypes($categoryTypes) {
		foreach ($categoryTypes as $ins_category_type) {
			if (
				isset($ins_category_type['statuscode']) && 
				$ins_category_type['statuscode']['bValue'] == 2
			) {
				continue;
			}
			$jobCategoryType = JobCategoryType::firstOrNew(['ins_job_category_type_id' => $ins_category_type['new_jobcategorytypeid']]);
			$jobCategoryType->category_type_name = array_get($ins_category_type, 'new_name');
			$jobCategoryType->ins_job_category_type_id = array_get($ins_category_type, 'new_jobcategorytypeid');
			$jobCategoryType->is_active = 1;	
			$jobCategoryType->save();
		}
	}
	
	public function importLocations($locations) {
		foreach ($locations as $location) {
			if (
				isset($location['statuscode']) && 
				$location['statuscode']['bValue'] == 2
			) {
				continue;
			}
			$insLocation  = InsLocation::firstOrNew(['ins_location_id' => $location['new_locationid']]);
			$insLocation->location = array_get($location, 'new_name');
			$insLocation->ins_location_id = array_get($location, 'new_locationid');
			$insLocation->is_active = 1;
			$insLocation->save();
		}
	}

	public function importSuburbs($suburbs) {
		foreach ($suburbs as $suburb) {
			if (
				isset($suburb['statuscode']) && 
				$suburb['statuscode']['bValue'] == 2
			) {
				continue;
			}
			$insLocation = InsSuburb::firstOrNew(['ins_suburb_id' => $suburb['new_suburbid']]);
			$insLocation->suburb = array_get($suburb, 'new_name');
			$insLocation->ins_suburb_id = array_get($suburb, 'new_suburbid');
			$insLocation->is_active = 1;
			$insLocation->save();
		}
	}

	public function importSkills($skills) {
		if (!empty($skills)) {
			foreach ($skills as $skill) {
				if (isset($skill['statuscode']) && $skill['statuscode']['bValue'] == 2) {
					continue;
				} 
				if (isset($skill['ins_occupationalcategory']['bId'])) {
					$category_id = $skill['ins_occupationalcategory']['bId'];
					$jobCategory  = JobCategory::where('ins_job_category_id',$category_id)->first();
					if (!empty($jobCategory)) {
						$ins_job_category_id = $jobCategory->id;
						$insSkill  = InsSkillAssementTypes::firstOrNew(['ins_skill_id' => $skill['ins_skillid']]);
						$insSkill->job_category_id =$ins_job_category_id;
						$insSkill->skil_names = array_get($skill, 'ins_name');
						$insSkill->ins_skill_id = $skill['ins_skillid'];
						$insSkill->is_active = 1;
						$insSkill->save();
					}
				}
			}
		}
	}
	
	public function importAgencies($agencies) {
		foreach ($agencies as $agency) {
			if (
				isset($agency['statuscode']) &&
				$agency['statuscode']['bValue'] == 2
			) {
				continue;
			}
			$insAgency  = AgencyDetails::firstOrNew(['ins_agency_id' => $agency['new_agencyid']]);
			$insAgency->agency_name = array_get($agency, 'new_name');
			$insAgency->ins_agency_id = array_get($agency, 'new_agencyid');
			$insAgency->is_active = 1;
			$insAgency->save();
		}
	}
}
