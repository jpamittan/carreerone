<?php
namespace App\Providers;

/**
 * Data Provider for all class constants - Usage aliased as Constants
 */
class ConstantDataProvider {
     
    /**
     * Exception codes
     */
    const RESP_CODE_DEFAULT_SUCCESS = 0;
    const RESP_CODE_DEFAULT_ERROR = 1;
    const RESP_CODE_DEFAULT_INFO = 2;
    const RESP_CODE_DEFAULT_DEBUG = 3;
    const RESP_CODE_AUTH_SUCCESS = 10;
    const RESP_CODE_AUTH_FAILURE = 10;
    const RESP_CODE_VALIDATION_ERROR = 11;
    const RESP_CODE_ACCOUNT_SUSPENDED = 10;
    const RESP_CODE_ACCOUNT_EMPLOYEE_TERMINATED = 13;
    const RESP_CODE_API_ERROR = 12;
    const RESP_CODE_DEFAULT_EXCEPTION = 500;
    const RESP_ADMIN_ACTION = 15;
    const RESP_CODE_VIRUS_SCAN_ERROR = 21;
    const RESP_CODE_TERMINATION_COMPLETED = 25;
    const QUEUE_MAX_ATTEMPTS = 98;
    const QUEUE_CODE_EXCEPTION = 99;

    /**
     * SOURCE
     */
    const SOURCE_REQUEST_TAKE = 50;
    const SOURCE_SALESFORCE = 'SALESFORCE';
    const SOURCE_BULLHORN = 'BULLHORN';
    const SOURCE_REQUEST_TYPE_ACCOUNT = 'Account';
    const SOURCE_REQUEST_TYPE_CONTACT = "Contact";
    const SOURCE_REQUEST_TYPE_ATTACHMENT = "Attachment";
    const SOURCE_REQUEST_TYPE_TASK = "Task";
    const SOURCE_REQUEST_TYPE_LEADS = 'Leads';
    const SOURCE_REQUEST_TYPE_CANDIDATES = 'Candidate';
    const SOURCE_REQUEST_STATUS_NEW = 0;
    const SOURCE_REQUEST_STATUS_IN_PROCESS= 1;
    const SOURCE_REQUEST_STATUS_DONE = 2;
    const BULLHORN_DEFAULT_COMPANY_STATUS ="Activation Required";
    const BULLHORN_DEFAULT_LEAD_STATUS = 'Identified for Licencee Progra';
    const BULLHORN_LEAD_SOURCE_PRS = 'PRS';
    const BULLHORN_LEAD_SOURCE_PARTNER = 'Email';
    const BULLHORN_DNU_STATUS = 'DNU';
    const POST_REQUEST_NEW = "NEW";
    const POST_REQUEST_UPDATE = "UPDATE";
    const PLACEMENT_TASK_SUBJECT  = 'Create Opportunity for Placement';
 
    public static function getStatsLoc()  {
        return array(
            'ClientSalesforce.pullCompanies' => 'Salesforce new companies' ,
            'ClientSalesforce.UpdateExistingCompanies' => 'Salesforce existing companies updates',
            'ClientSalesforce.UpdateOpportunityStatus' => 'Salesforce opportunity updates',
            'ClientSalesforce.pullContacts' => 'Salesforce new contacts',
            'ClientSalesforce.UpdateExistingContacts' => 'Salesforce existing contacts updates',
            'ClientSalesforce.pullAttachments' => 'Salesforce new attachments',
            'ClientSalesforce.UpdateExistingAttachments' => 'Salesforce existing attachments',
            'BullhornCompany.pushNewCompanies' => 'Bullhorn new companies',
            'BullhornCompany.updateCompanies' => 'Bullhorn existing companies update',
            'BullhornCompany.pushNewContacts' => 'Bullhorn new contacts',
            'BullhornCompany.updateContacts' => 'Bullhorn existing contacts update', 
            'BullhornCompany.pushNewAttachments' => 'Bullhorn new attachments', 
            'BullhornCompany.updateAttachments' => 'Bullhorn existing attachments',
            'BullhornLead.pushNewLeads'  => 'Bullhorn new leads', 
            'BullhornCandidate.pushNewCandidates' => 'Bullhorn Candidates'
        );
    }

    public static function bhContryCode() {
        return array(
            'New Zealand' => 2307,
            'Australia' => 2194
        );
    }

    public static function ContrybyBhCode() {
        return array_flip(self::bhContryCode());
    }
}
