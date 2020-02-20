<?php
namespace App\Models\Containers;

/**
 * Container class for holding extracted information from resume
 */
class ResumeExtract {
    /**
     * PLaceholder for profile id
     * @var integer
     */
    public $profile_id = null;

    /**
     * Placeholder for personal info
     * @var mixed
     */
    public $personal_info = array();

    /**
     * Placeholder for address info
     * @var mixed
     */
    public $address_info = array();

    /**
     * Placeholder for educational Information
     * @var mixed
     */
    public $education_info = array();

    /**
     * Placeholder for work history Information
     * @var mixed
     */
    public $work_history = array();

    /**
     * Placeholder for skills Information
     * @var mixed
     */
    public $skills = array();

    /**
     * Placeholder for extra information
     * @var mixed
     */
    public $extra_info = array();

    /**
     * Placeholder for industries
     * @var array
     */
    public $industries = array();

    /**
     * Function to find gender
     * @return string
     */
    public function getGender() {
        return (isset($this->personal_info['gender'])) ? $this->personal_info['gender'] : '';
    }

    /**
     * Function to find contact number
     * @return string
     */
    public function getContactNumber() {
        if (isset($this->personal_info['phone_number'])) {
            if (is_array($this->personal_info['phone_number'])) {
                return $this->personal_info['phone_number'][0];
            } else {
                return $this->personal_info['phone_number'];
            }
        }
    }

    /**
     * Function to get most recent job title
     * @return string
     */
    public function getMostRecentJobTitle() {
        if (empty($this->work_history)) {
            return '';
        }
        return $this->work_history;
        foreach ($this->work_history as $work_history) {
            if (!empty($work_history['job_title'])) {
                return $work_history['job_title'];
            }
        }
        return '';
    }

    /**
     * Function to get hight education level
     * @return string
     */
    public function getHighestEducation() {
        if (empty($this->education_info)) {
            return '';
        }
        return $this->education_info;
        foreach ($this->education_info as $education_info) {
            if (!empty($education_info['qualification'])) {
                return $education_info['qualification'];
            }
        }
        return '';
    }

    public function getPhoneNumber() {
        $phone_number = array_get($this->personal_info, 'phone_number');
        if (is_array($phone_number)) {
            return $phone_number[0];
        } else {
            return $phone_number;
        }
    }

    public function getPersonalEmail() {
        $email = array_get($this->personal_info, 'personal_email');
        $email_arr = explode(';', $email);
        return $email_arr[0];
    }

    public function getPersonalInfo($index) {
        $method_check = 'get' . studly_case($index);
        if (method_exists($this, $method_check)) {
            $value = $this->$method_check();
        }
        return (isset($this->personal_info[$index])) ? $this->personal_info[$index] : null;
    }

    public function getAddressInfo($index) {
        $method_check = 'get' . studly_case($index);
        if (method_exists($this, $method_check)) {
            $value = $this->$method_check();
        }
        return (isset($this->address_info[0]) && isset($this->address_info[0][$index])) ? $this->address_info[0][$index] : null;
    }

    /**
     * Function to get hight education level
     * @return string
     */
    public function getLocation() {
        $location = array(
            'city' => null,
            'state' => null,
            'postcode' => null,
        );
        if (empty($this->address_info)) {
            return $location;
        }
        $address = array();
        if (is_array($this->address_info)) {
            $address = $this->address_info[0];
        } else {
            $address = $this->address_info;
        }
        $location['city'] = array_get($address, 'suburb', null);
        $location['state'] = array_get($address, 'state', null);
        $location['postcode'] = array_get($address, 'postcode', null);
        return $location;
    }
}
