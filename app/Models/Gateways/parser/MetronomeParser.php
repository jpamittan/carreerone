<?php

namespace Application\Models\Gateways\Parser;

use Application\Models\Gateways\SAXParserGateway;
use Application\Models\Entities\Course;
use DB, Config, Event;

class MetronomeParser extends SAXParserGateway
{
    protected $default_row = [
        'metronome_id' => '',
        'title' => '',
        'description_html' => '',
        'categories' => '',
        'enrolment' => '',
        'outcomes_html' => '',
        'url' => '',
        'provider' => '',
        'provider_slug' => '',
        'type' => '',
        'code' => '',
        'accreditation' => '',
        'entry_requirements_html' => '',
        'course_structure_html' => '',
        'course_units_subjects_html' => '',
        'career_opportunities_html' => '',
        'payment_options_html' => '',
        'recognition_prior_learning_html' => '',
        'potential_pathways_html' => '',
        'fees_policies_refunds_html' => '',
    	'govt_assistance' => '',
        'locations' => '',
        'study_modes' => '',
        'deleted_at' => '',
        'created_at' => '',
        'updated_at' => '',
    ];

    protected $insert_cache = [];
    protected $delete_exclude_cache = [];

    public function __construct()
    {
        $this->insert_date = date('Y-m-d H:i:s');
        $this->insert_cache_limit = Config::get('careerone.metronome.import_insert_limit');
        $this->default_row['created_at'] = $this->insert_date;
        $this->default_row['updated_at'] = $this->insert_date;
    }

    public function startTag($parser, $tag_name, $attributes)
    {
        $this->openedTag[$tag_name] = $tag_name;
        switch ($tag_name) {
            case 'Course':
                $this->course = $this->default_row;
                $this->course['source'] = 'metronome';
                $this->started = true;
                break;
        }
        $this->currentTag = $tag_name;
    }

    public function endTag($parser, $tag_name)
    {
        if ($tag_name == 'Course') {
            $this->insert_cache[] = $this->course;
            $this->delete_exclude_cache[] = $this->course['metronome_id'];
            $this->course = [];
            if (count($this->insert_cache) >= $this->insert_cache_limit) {
                $this->processInsertCache();
            }
        }

        $this->currentTag = '';
        unset($this->openedTag[$tag_name]);
    }

    public function tagData($parser, $data)
    {
        if ($this->currentTag != '' && $this->started) {
            switch ($this->currentTag) {
                case 'id':
                    $this->course['metronome_id'] .= $data;
                    break;
                case 'title':
                    $this->course['title'] .= $data;
                    break;
                case 'description':
                    $this->course['description_html'] .= $data;
                    break;
                case 'categories':
                    $this->course['categories'] .= $data;
                    break;
                case 'enrolment':
                    $this->course['enrolment'] .= $data;
                    break;
                case 'outcomes':
                    $this->course['outcomes_html'] .= $data;
                    break;
                case 'url':
                    $this->course['url'] .= $data;
                    break;
                case 'provider':
                    $this->course['provider'] .= $data;
                    break;
                case 'provider-slug':
                    $this->course['provider_slug'] .= $data;
                    break;
                case 'coursetype':
                    $this->course['type'] .= $data;
                    break;
                case 'CourseCode':
                    $this->course['code'] .= $data;
                    break;
                case 'ProviderInformationandAccreditation':
                    $this->course['accreditation'] .= $data;
                    break;
                case 'EntryRequirements':
                    $this->course['entry_requirements_html'] .= $data;
                    break;
                case 'CourseStructure':
                    $this->course['course_structure_html'] .= $data;
                    break;
                case 'CourseUnitsSubjects':
                    $this->course['course_units_subjects_html'] .= $data;
                    break;
                case 'CareerOpportunities':
                    $this->course['career_opportunities_html'] .= $data;
                    break;
                case 'GovernmentAssistance':
                    $this->course['govt_assistance'] .= $data;
                    break;
                case 'Locations':
                    $this->course['locations'] .= $data;
                    break;
                case 'StudyModes':
                    $this->course['study_modes'] .= $data;
                    break;
                case 'c1-payment-options':
                    $this->course['payment_options_html'] .= $data;
                    break;
                case 'RecognitionofPriorLearningCreditTransfer':
                    $this->course['recognition_prior_learning_html'] .= $data;
                    break;
                case 'PotentialCareerandEducationPathways':
                    $this->course['potential_pathways_html'] .= $data;
                    break;
                case 'CourseFeesPoliciesRefunds':
                    $this->course['fees_policies_refunds_html'] .= $data;
                    break;
            }
        }
    }

    public function processInsertCache()
    {
        if (count($this->insert_cache)) {
            $inserts = [];
            $updates = [];
            $fields = array_keys($this->insert_cache[0]);

            foreach ($fields as $field) {
                if ($field == 'created_at') continue; // don't update created_at field
                $updates[] = sprintf("%s = VALUES(%s)", $field, $field);
            }

            foreach ($this->insert_cache as $key => $row) {
                foreach ($row as $row_key => $value) {
                    if (strpos($row_key, '_at') !== false && empty($value)) {
                        $row[$row_key] = "NULL";
                    } else {
                        $row[$row_key] = DB::getPdo()->quote($value);
                    }
                }
                $inserts[] = "(" . implode(", ", $row) . ")";
            }

            $sql = "INSERT INTO "
                    . DB::getTablePrefix() . "courses (" . implode(', ', $fields) . ") VALUES " . implode(", ", $inserts)
                    . " ON DUPLICATE KEY UPDATE " . implode(', ', $updates);
            
            DB::statement($sql);

            unset($this->insert_cache);
            $this->insert_cache = [];
        }
    }

    public function processDeleteCache()
    {
        if (count($this->delete_exclude_cache)) {
            Course::whereNotIn('metronome_id', $this->delete_exclude_cache)->delete();
            unset($this->delete_exclude_cache);
            $this->delete_exclude_cache = [];
        }
    }
}