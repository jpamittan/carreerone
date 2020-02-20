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
        <JobSeeker xmlns="http://schemas.monster.com/Monster" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://schemas.monster.com/Monster http://schemas.monster.com/Current/XSD/JobSeeker.xsd" status="active" seekerAction="addOrUpdate" seekerRefCode="{{substr($user->first_name.$user->last_name,0,17)}}{{$category}}">
            <Channel monsterId="10645"/>
            <PersonalData>
                <Contact>
                    <StructuredName>
                        <GivenName>{{$user->first_name}}</GivenName>
                        <FamilyName>{{$user->last_name}}</FamilyName>
                    </StructuredName>
                    <Address>
                        <StreetAddress/>
                        <City>Sydney</City>
                        <State>NSW</State>
                        <CountryCode>AU</CountryCode>
                        <PostalCode>2000</PostalCode>
                    </Address>
                    <E-mail>{{str_replace('@', '+' . $category . '@', $user->email)}}</E-mail>
                </Contact>
            </PersonalData>
            <Account>
                <Username>{{substr($user->first_name.$user->last_name,0,17)}}{{$category}}</Username>
            </Account>
            <Resumes>
                <Resume resumeAction="addOrUpdate" resumeRefCode="880004">
                    <BoardName monsterId="11517"/>
                    <ResumeTitle>{{$filename}}</ResumeTitle>
                    <ResumeModDate>{{$modified_date}}</ResumeModDate>
                    <ResumeDocument documentAction="add">
                        <File>{{$content}}</File>
                        <FileName>{{$filename}}</FileName>
                        <FileContentType>{{$mimetype}}</FileContentType>
                    </ResumeDocument>
                </Resume>
            </Resumes>
        </JobSeeker>
    </SOAPENV:Body>
</SOAPENV:Envelope>