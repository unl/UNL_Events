	<Event>
        <EventID><?php echo $context->event->id; ?></EventID>
        <EventTitle><?php echo htmlspecialchars($context->event->displayTitle($context)); ?></EventTitle>
        <EventSubtitle><?php echo htmlspecialchars($context->event->subtitle); ?></EventSubtitle>
        <Status><?php echo $context->event->icalStatus($context); ?></Status>
        <?php
        $timezoneDateTime = new \UNL\UCBCN\TimezoneDateTime($context->eventdatetime->timezone);
		    ?>
        <DateTime>
            <StartDate><?php echo $timezoneDateTime->format($context->getStartTime(),'c'); ?></StartDate>
            <StartTime><?php echo $timezoneDateTime->formatUTC($context->getStartTime(),'H:i:s'); ?>Z</StartTime>
            <?php if (isset($context->eventdatetime->endtime)
                    && !empty($context->eventdatetime->endtime)
                    && ($context->getEndTime() > $context->getStartTime())) : ?>
            <EndDate><?php echo $timezoneDateTime->format($context->getEndTime(),'c'); ?></EndDate>
            <EndTime><?php echo $timezoneDateTime->formatUTC($context->getEndTime(),'H:i:s'); ?>Z</EndTime>
            <?php endif; ?>
        </DateTime>
        <Locations>
        	<?php
			if (isset($context->eventdatetime->location_id) && !empty($context->eventdatetime->location_id)) :
                $loc = $context->eventdatetime->getLocation();
			?>
            <Location>
                <LocationID><?php echo $loc->id; ?></LocationID>
                <LocationName><?php echo htmlspecialchars($loc->name); ?></LocationName>
                <LocationTypes>
                    <LocationType><?php echo $loc->type; ?></LocationType>
                </LocationTypes>
                <Address>
                    <Room><?php echo htmlspecialchars($context->eventdatetime->room); ?></Room>
                    <BuildingName><?php echo htmlspecialchars($loc->name); ?></BuildingName>
                    <CityName><?php echo htmlspecialchars($loc->city); ?></CityName>
                    <PostalZone><?php echo $loc->zip; ?></PostalZone>
                    <CountrySubentityCode><?php echo $loc->state; ?></CountrySubentityCode>
                    <Country>
                        <IdentificationCode xmlns="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-1.0" codeListID="ISO3166-1" codeListAgencyID="6" codeListAgencyName="United Nations Economic Commission for Europe" codeListName="Country" codeListVersionID="0.3" languageID="en" codeListURI="http://www.iso.org/iso/en/prods-services/iso3166ma/02iso-3166-code-lists/list-en1-semic.txt" codeListSchemeURI="urn:oasis:names:specification:ubl:schema:xsd:CountryIdentificationCode-1.0">US</IdentificationCode>
                        <Name xmlns="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-1.0">United States</Name>
                    </Country>
                </Address>
                <Phones>
                    <Phone>
                        <PhoneNumber><?php echo $loc->phone; ?></PhoneNumber>
                    </Phone>
                </Phones>

                <WebPages>
                    <WebPage>
                        <Title>Location Web Page</Title>
                        <URL><?php echo htmlspecialchars($loc->webpageurl); ?></URL>
                    </WebPage>
                </WebPages>
                <MapLinks>
                    <MapLink><?php echo htmlspecialchars($loc->mapurl); ?></MapLink>
                </MapLinks>

                <LocationHours><?php echo htmlspecialchars($loc->hours); ?></LocationHours>
                <Directions><?php echo htmlspecialchars($loc->directions); ?></Directions>
                <AdditionalPublicInfo><?php echo htmlspecialchars($loc->additionalpublicinfo); ?></AdditionalPublicInfo>
            </Location>
            <?php endif; ?>
        </Locations>
        <?php
        $eventTypes = $context->event->getEventTypes();
        if ($eventTypes->count()) : ?>
        <EventTypes>
        	<?php foreach ($eventTypes as $eventHasType) : 
        		$type = $eventHasType->getType();
	        	if ($type) : ?>
	            <EventType>
	                <EventTypeID><?php echo $type->id; ?></EventTypeID>
	                <EventTypeName><?php echo htmlspecialchars($type->name); ?></EventTypeName>
	                <EventTypeDescription><?php echo htmlspecialchars($type->description); ?></EventTypeDescription>
	            </EventType>
	            <?php 
            	endif;
            endforeach; ?>
        </EventTypes>
        <?php endif; ?>
        <?php
        $audiences = $context->event->getAudiences();
        if ($audiences->count()) : ?>
        <Audiences>
            <?php foreach ($audiences as $audience) : ?>
                <?php $current_audience = $audience->getAudience(); ?>
                <?php if($current_audience): ?>
	            <Audience>
	                <AudienceID><?php echo $current_audience->id; ?></AudienceID>
	                <AudienceName><?php echo htmlspecialchars($current_audience->name); ?></AudienceName>
	            </Audience>
                <?php endif; ?>
            <?php endforeach; ?>
        </Audiences>
        <?php endif; ?>
        <Languages>
            <Language>en-US</Language>
        </Languages>
        <EventTransparency><?php echo $context->event->transparency; ?></EventTransparency>

        <Description><?php echo htmlspecialchars($context->event->description); ?></Description>
        <ShortDescription><?php echo htmlspecialchars($context->event->shortdescription); ?></ShortDescription>
        <Refreshments><?php echo htmlspecialchars($context->event->refreshments); ?></Refreshments>
        <WebPages>
            <WebPage>
                <Title>Event Instance URL</Title>
                <URL><?php echo htmlspecialchars($context->getURL()); ?></URL>
            </WebPage>
            <?php if (!empty($context->event->webpageurl)): ?>
            <WebPage>
                <Title>Event webpage</Title>
                <URL><?php echo htmlspecialchars($context->event->webpageurl); ?></URL>
            </WebPage>
            <?php endif; ?>
        </WebPages>
        <Webcasts>
        <?php
            if (isset($context->eventdatetime->webcast_id) && !empty($context->eventdatetime->webcast_id)) :
                $webcast = $context->eventdatetime->getWebcast();
            ?>
            <Webcast>
                <WebcastID><?php echo $webcast->id; ?></WebcastID>
                <WebcastName><?php echo htmlspecialchars($webcast->title); ?></WebcastName>
                <WebcastURL><?php echo htmlspecialchars($webcast->url); ?></WebcastURL>
                <AdditionalPublicInfo><?php echo htmlspecialchars($webcast->additionalinfo); ?></AdditionalPublicInfo>
            </Webcast>
            <?php endif; ?>
        </Webcasts>
        <?php if (!empty($context->event->imagedata)) : ?>
        <Images>
            <Image>
                <Title>Image</Title>
                <Description>image for event <?php echo $context->event->id; ?></Description>
                <URL><?php echo \UNL\UCBCN\Frontend\Controller::$url; ?>?image&amp;id=<?php echo $context->event->id; ?></URL>
            </Image>
        </Images>
        <?php endif; ?>
        <?php
        $documents = $context->event->getDocuments();
        if ($documents->count()) : ?>
        <Documents>
        	<?php foreach ($documents as $document) : ?>
            <Document>
                <Title><?php echo htmlspecialchars($document->name); ?></Title>
                <URL><?php echo $document->url; ?></URL>
            </Document>
            <?php endforeach; ?>
        </Documents>
        <?php endif; ?>
        <?php
        $contacts = $context->event->getPublicContacts();
        if ($contacts->count()) : ?>
        <PublicEventContacts>
        	<?php foreach ($contacts as $contact) : ?>
            <PublicEventContact>
                <PublicEventContactID><?php echo $contact->id; ?></PublicEventContactID>

                <ContactName>
                    <FullName><?php echo htmlspecialchars($contact->name); ?></FullName>
                </ContactName>
                <ProfessionalAffiliations>
                    <ProfessionalAffiliation>

                        <JobTitles>
                            <JobTitle><?php echo htmlspecialchars($contact->jobtitle); ?></JobTitle>
                        </JobTitles>
                        <OrganizationName><?php echo htmlspecialchars($contact->organization); ?></OrganizationName>
                        <OrganizationWebPages>
                            <WebPage>

                                <Title><?php echo htmlspecialchars($contact->name); ?></Title>
                                <URL><?php echo $contact->webpageurl; ?></URL>
                            </WebPage>

                        </OrganizationWebPages>
                    </ProfessionalAffiliation>
                </ProfessionalAffiliations>
                <Phones>
                    <Phone>
                        <PhoneNumber><?php echo htmlspecialchars($contact->phone); ?></PhoneNumber>
                    </Phone>
                </Phones>

                <EmailAddresses>
                    <EmailAddress><?php echo $contact->emailaddress; ?></EmailAddress>
                </EmailAddresses>
                <Addresses>
                    <Address>
                        <StreetName><?php echo htmlspecialchars($contact->addressline1); ?></StreetName>
                        <AdditionalStreetName><?php echo htmlspecialchars($contact->addressline2); ?></AdditionalStreetName>
                        <Room><?php echo htmlspecialchars($contact->room); ?></Room>
                        <CityName><?php echo htmlspecialchars($contact->city); ?></CityName>
                        <PostalZone><?php echo $contact->zip; ?></PostalZone>
                        <CountrySubentityCode><?php echo htmlspecialchars($contact->State); ?></CountrySubentityCode>
                        <Country>
                            <IdentificationCode xmlns="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-1.0" codeListID="ISO3166-1" codeListAgencyID="6" codeListAgencyName="United Nations Economic Commission for Europe" codeListName="Country" codeListVersionID="0.3" languageID="en" codeListURI="http://www.iso.org/iso/en/prods-services/iso3166ma/02iso-3166-code-lists/list-en1-semic.txt" codeListSchemeURI="urn:oasis:names:specification:ubl:schema:xsd:CountryIdentificationCode-1.0">US</IdentificationCode>

                            <Name xmlns="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-1.0">United States</Name>
                        </Country>
                    </Address>
                </Addresses>
                <WebPages>
                    <WebPage>
                        <Title><?php echo htmlspecialchars($contact->name); ?></Title>
                        <URL><?php echo $contact->webpageurl; ?></URL>
                    </WebPage>
                </WebPages>
            </PublicEventContact>
            <?php endforeach; ?>
        </PublicEventContacts>
        <?php endif; ?>
        <EventListingContacts>

            <EventListingContact>
                <ContactName>
                    <FullName><?php echo htmlspecialchars($context->event->listingcontactname); ?></FullName>
                </ContactName>
                <Phones>
                    <Phone>
                        <PhoneNumber><?php echo htmlspecialchars($context->event->listingcontactphone); ?></PhoneNumber>
                    </Phone>
                </Phones>
                <EmailAddresses>
                    <EmailAddress><?php echo $context->event->listingcontactemail; ?></EmailAddress>
                </EmailAddresses>
            </EventListingContact>
        </EventListingContacts>
        <EventStatus>Happening As Scheduled</EventStatus>
        <Classification>Public</Classification>
        <?php if (!empty($context->event->privatecomment)): ?>
        <PrivateComments>
            <PrivateComment><?php echo htmlspecialchars($context->event->privatecomment); ?></PrivateComment>
        </PrivateComments>
        <?php endif; ?>
        <?php
        $originCalendar = $context->event->getOriginCalendar();
        $protocol = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
        ?>
        <?php if ($originCalendar instanceof \UNL\UCBCN\Calendar): ?>
            <OriginCalendar>
                <CalendarID><?php echo $originCalendar->id; ?></CalendarID>
                <AccountID><?php echo $originCalendar->account_id; ?></AccountID>
                <Name><?php echo htmlspecialchars($originCalendar->name); ?></Name>
                <ShortName><?php echo htmlspecialchars($originCalendar->shortname); ?></ShortName>
                <URL><?php echo $protocol . $_SERVER['SERVER_NAME'] . '/' . urlencode($originCalendar->shortname);?></URL>
            </OriginCalendar>
        <?php endif; ?>
    </Event>
