<?php
namespace Application\Models\Gateways\Profile;

use Format;
use Application\Models\Containers\ResumeExtract;

/**
 * Gateway class for extracing linked in resume
 */
class LinkedInProfile
{
  /**
   * Placholder for raw data
   * @var mixed
   */
  protected $raw_data = null;

  /**
   * Placeholder array for extracted profile info
   * @var mixed
   */
  protected $extract = null;

  /**
   *
   */
  public function __construct()
  {
    $this->extract = new ResumeExtract();
  }

  /**
   * Function thats exposed to load data from source in differnet formats
   * @param mixed $data
   */
  public function load($data)
  {
    $this->raw_data = $data;
  }

  /**
   * Parse function that been exposed to parse raw data
   * @return ResumeExtract
   */
  public function parse()
  {
    // ignore data
    // $first_name = $this->raw_data->values["firstName"];
    // $last_name = $this->raw_data->values["lastName"];
    // $location = $this->raw_data->values["location"];
    // $interests = $this->raw_data->values["interests"];
    // $certifications = $this->raw_data->values["certifications"];
    // $headline = $this->raw_data->values["headline"];

    // parse personal info
    $this->parsePersonalInfo($this->raw_data->values[0]);

    // parse education
    $education_info = data_get($this->raw_data->values[0], "educations", null);
    $this->parseEducationInfo($education_info);

    // parse workhistory
    $work_history = data_get($this->raw_data->values[0], "positions", null);
    $this->parseWorkHistoryInfo($work_history);

    // parse skills
    $skills = data_get($this->raw_data->values[0], "skills", null);
    $this->parseSkillsInfo($skills);

    return $this->extract;
  }

  /**
   * Private function to parse personal information
   * @param stdClass $input
   * @return boolean
   */
  private function parsePersonalInfo($input)
  {
    if (is_null($input)) {
      return false;
    }

    // email and summary
    $this->extract->personal_info['personal_email'] = data_get($input, "emailAddress", null);
    $this->extract->personal_info['career_summary'] = data_get($input, "summary", null);

    // save dob`
    $dob = data_get($input, "dateOfBirth", null);
    $year = data_get($dob, 'year', null);
    $month = data_get($dob, 'month', null);
    $day = data_get($dob, 'day', null);
    $this->extract->personal_info['dob'] = Format::createDate($year, $month, $day);

    // save contact info
    $phone_numbers = data_get($input, "phoneNumbers", null);
    if (isset($phone_numbers->values)) {
      foreach ($phone_numbers->values as $key => $info) {
        if ($info->phoneType == 'mobile') {
          $this->extract->personal_info['mobile_number'] = $info->phoneNumber;
        } else if ($info->phoneType == 'work') {
          $this->extract->personal_info['work_number'] = $info->work_number;
        } else {
          $this->extract->personal_info['home_number'] = $info->home_number;
        }
      }
    }
  }

  /**
   * Private function to parse education information
   * @param stdClass $input
   * @return boolean
   */
  private function parseEducationInfo($input)
  {
    if (is_null($input)) {
      return false;
    }

    $educations = array();
    foreach ($input->values as $key => $info) {
      $educations[$key]['institution'] = data_get($info, 'schoolName', null);
      $educations[$key]['description'] = data_get($info, 'fieldOfStudy', null);
      $educations[$key]['education_level'] = data_get($info, 'degree', null); // need to map to an id

      // save start date
      $start_date = data_get($info, 'startDate', null);
      if ($start_date) {
        $year = data_get($start_date, 'year', null);
        $month = data_get($start_date, 'month', null);
        $educations[$key]['start_date'] = Format::createDate($year, $month);
      }

      // save end date
      $end_date = data_get($info, 'endDate', null);
      if ($end_date) {
        $year = data_get($end_date, 'year', null);
        $month = data_get($end_date, 'month', null);
        $educations[$key]['end_date'] = Format::createDate($year, $month);
      }
    }
    $this->extract->education_info = $educations;
  }

  /**
   * Private function to parse work history information
   * @param stdClass $input
   * @return boolean
   */
  private function parseWorkHistoryInfo($input)
  {
    if (is_null($input) or count($input) == 0) {
      return false;
    }

    $work_history = array();
    foreach ($input->values as $key => $info) {
      $work_history[$key]['description'] = data_get($info, 'summary', null);
      $work_history[$key]['job_title'] = data_get($info, 'title', null);

      // mark job as current
      $active_job = data_get($info, 'isCurrent', false);
      $work_history[$key]['active_job'] = ($active_job) ? 1 : 0;

      // save company info
      $company = data_get($info, 'company', null);
      if ($company) {
        $work_history[$key]['organisation'] = $company->name;
      }

      // save start date
      $start_date = data_get($info, 'startDate', null);
      if ($start_date) {
        $year = data_get($start_date, 'year', null);
        $month = data_get($start_date, 'month', null);
        $work_history[$key]['start_date'] = Format::createDate($year, $month);
      }

      // save end date
      $end_date = data_get($info, 'endDate', null);
      if ($end_date) {
        $year = data_get($end_date, 'year', null);
        $month = data_get($end_date, 'month', null);
        $work_history[$key]['end_date'] = Format::createDate($year, $month);
      }
    }
    $this->extract->work_history = $work_history;
  }

  /**
   * Private function to parse skills information
   * @param stdClass $input
   * @return boolean
   */
  private function parseSkillsInfo($input)
  {
    if (is_null($input) or count($input) == 0) {
      return false;
    }

    $skills = array();
    foreach ($input->values as $key => $info) {
      $skill = $info->skill;
      $skills[] = $skill->name;
    }
    $this->extract->skills = $skills;
  }
}
