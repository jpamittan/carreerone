<SOAPENV:Envelope xmlns:SOAPENV="http://schemas.xmlsoap.org/soap/envelope/">
    <SOAPENV:Header>
        <mh:MonsterHeader xmlns:mh="http://schemas.monster.com/MonsterHeader">
          <mh:MessageData>
              <mh:MessageId>Jobseeker Private db sample</mh:MessageId>
              <mh:Timestamp>2016-11-21T14:41:44Z</mh:Timestamp>
          </mh:MessageData>
          <mh:ProcessingReceiptRequest>
              <mh:Address transportType="http">http</mh:Address>
          </mh:ProcessingReceiptRequest>
        </mh:MonsterHeader>
        <wsse:Security xmlns:wsse="http://schemas.xmlsoap.org/ws/2002/04/secext">
            <wsse:UsernameToken>
                <wsse:Username>xc1sm_insx01</wsse:Username>
                <wsse:Password>rxc18976</wsse:Password>
            </wsse:UsernameToken>
        </wsse:Security>
    </SOAPENV:Header>
    <SOAPENV:Body>
        <JobSeekerDeleteByEmailAddress xmlns="http://schemas.monster.com/Monster"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
        xsi:schemaLocation="http://schemas.monster.com/Monster 
        http://schemas.monster.com/Current/xsd/Monster.xsd">
           <EmailAddress>{{str_replace('@', '+' . $category . '@', $user->email)}}</EmailAddress>
           <ChannelID>10645</ChannelID>
    </JobSeekerDeleteByEmailAddress>
    </SOAPENV:Body>
</SOAPENV:Envelope>