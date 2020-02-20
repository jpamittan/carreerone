<?php
namespace App\Models\Gateways;


class ICS {
    protected $data = null;
    protected $name = "calendar";
    protected $start  = null;
    protected $end  = null;
    protected $subject  = null;
    protected $description  = null;
    protected $location   = null;
    protected $filename   = null;
    protected $attendee_mail   = null;
     protected $attendee_name   = null;
    protected $organizer_mail   = null;
     protected $organizer_name   = null;

    

    public function __construct() {
         



    }

    public function setFilename($filename){
        $this->name = $this->filename =  $filename; 
         return $this;
    }
     public function setSubject($subject){
        $this->subject  =  $subject; 
         return $this;
    }

    public function setDescription($description){
        $this->description =  $description; 
         return $this;
    }
    public function setStart($start){
        $this->start =  $start; 
         return $this;
    }
    public function setEnd($end){
        $this->end =  $end; 
         return $this;
    }
    public function setLocation($location){
        $this->location =  $location; 
         return $this;
    }
    public function setOrganizer($organizer_mail , $organizer_name){
        $this->organizer_mail =  $organizer_mail; 
        $this->organizer_name =  $organizer_name; 
         return $this;
    }
    public function addAttendee($attendee_mail, $attendee_name){
       $this->attendee_mail =  $attendee_mail; 
        $this->attendee_name =  $attendee_name; 
         return $this;
    }
 
    



    public function getCalendar()
    {
        $description =  $this->description ;
         $eol = "\r\n";
         
         $desc = $this->escapeString($description) ;

        $this->data = "BEGIN:VCALENDAR". $eol .
"VERSION:2.0". $eol .
"PRODID:-//hacksw/handcal//NONSGML v1.0//EN". $eol .
"METHOD:PUBLISH". $eol .
//"ORGANIZER;CN='John Smith':mailto:jsmith@host1.com". $eol .
"BEGIN:VEVENT". $eol .
"DTSTART:".$this->dateToCal($this->start)."". $eol .
"DTEND:".$this->dateToCal($this->end)."". $eol .
"LOCATION:".$this->location."". $eol .
"TRANSP: OPAQUE". $eol .
"SEQUENCE:0". $eol .
"UID:".rand(). $eol .
"DTSTAMP:".$this->dateToCal(date("Y-m-d H:i:s"))."". $eol .
"ORGANIZER;CN=".$this->organizer_name.":mailto:".$this->organizer_mail. $eol .
"ATTENDEE;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN=".$this->attendee_name.";X-NUM-GUESTS=0:mailto:".$this->attendee_mail. $eol .
"SUMMARY:".$this->escapeString($this->subject)."". $eol .
"DESCRIPTION:".$desc."". $eol .
"X-ALT-DESC;FMTTYPE=text/html:".$desc."". $eol .
"PRIORITY:1". $eol .
"CLASS:PUBLIC". $eol .
"BEGIN:VALARM". $eol .
"TRIGGER:-PT10080M". $eol .
"ACTION:DISPLAY". $eol .
"DESCRIPTION:Reminder". $eol .
"END:VALARM". $eol .
"END:VEVENT". $eol .
"END:VCALENDAR" ;

    }
    public function save() {
        $this->getCalendar();
        $result = \File::makeDirectory(storage_path() . "/ics", 0775, true, true);
        $path =storage_path() . "/ics/". $this->name.".ics" ; 
        file_put_contents($path,$this->data);
        return $path;
    }
    public function show() {
        $this->getCalendar();

        header("Content-type:text/calendar");
        header('Content-Disposition: attachment; filename="'.$this->name.'.ics"');
        Header('Content-Length: '.strlen($this->data));
        Header('Connection: close');
        echo $this->data;
    }


    public function dateToCal($timestamp) {
      return date('Ymd\THis', strtotime($timestamp));
    }

    public function  escapeString($string) {
        $htmlMsg = $string;
        $temp = str_replace(array("\r\n"),"\\n",$htmlMsg);
        $lines = explode("\n",$temp);
        $new_lines =array();
        foreach($lines as $i => $line)
        {
            if(!empty($line))
            $new_lines[]=trim($line);
        }
        $desc = implode("\r\n ",$new_lines);
        return $desc;
    }

}

 