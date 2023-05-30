
DROP PROCEDURE IF EXISTS updateOrInsertLocation;
DELIMITER //

CREATE PROCEDURE updateOrInsertLocation(
    IN in_name VARCHAR(100),
    IN in_streetaddress1 VARCHAR(255),
    IN in_city VARCHAR(100),
    IN in_state VARCHAR(2),
    IN in_zip VARCHAR(10),
    IN in_mapurl longtext,
    IN in_additionalpublicinfo VARCHAR(255),
    IN in_standard TINYINT
)
BEGIN
    SELECT @A:=id FROM location WHERE name = in_name AND standard = 1 LIMIT 1;
    IF EXISTS (SELECT id FROM location WHERE name = in_name AND standard = 1) THEN
        UPDATE location
        SET streetaddress1 = in_streetaddress1,
            city = in_city,
            state = in_state,
            zip = in_zip,
            mapurl = in_mapurl,
            additionalpublicinfo = in_additionalpublicinfo
        WHERE id = @A;
    ELSE
        INSERT INTO location (name, streetaddress1, city, state, zip, mapurl, additionalpublicinfo, standard)
        VALUES (in_name, in_streetaddress1, in_city, in_state, in_zip, in_mapurl, in_additionalpublicinfo, in_standard);
    END IF;

END//

DELIMITER ;

START transaction;

CALL updateOrInsertLocation('Chase Hall', '3605 Fair St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CHA', 'CHA', '1');
CALL updateOrInsertLocation('Agricultural Hall', '3550 East Campus Loop S', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/AGH', 'AGH', '1');
CALL updateOrInsertLocation('Dinsdale Family Learning Commons', '1625 N 38th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/DINS', 'DINS', '1');
CALL updateOrInsertLocation('Kiesselbach Crops Research Laboratory', '1870 N 37th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/KCR', 'KCR', '1');
CALL updateOrInsertLocation('Agricultural Communications Building', '3620 East Campus Loop S', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/ACB', 'ACB', '1');
CALL updateOrInsertLocation('Leverton Hall', '1700 N 35th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/LEV', 'LEV', '1');
CALL updateOrInsertLocation('Plant Science Teaching Greenhouse', '3855 Fair St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/PSTG', 'PSTG', '1');
CALL updateOrInsertLocation('Teaching Greenhouse West', '3850 Center Dr', 'Lincoln', 'NE', '68510', 'https://maps.unl.edu/TGW', 'TGW', '1');
CALL updateOrInsertLocation('Teaching Greenhouse East', '3850 Center Dr', 'Lincoln', 'NE', '68510', 'https://maps.unl.edu/TGE', 'TGE', '1');
CALL updateOrInsertLocation('Utility Plant, East Campus', '1935 N 37th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/ECUP', 'ECUP', '1');
CALL updateOrInsertLocation('Family Resource Center', '1615 N 35th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/FRC', 'FRC', '1');
CALL updateOrInsertLocation('Insectary Building', '3865 Fair St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/INSB', 'INSB', '1');
CALL updateOrInsertLocation('Keim Hall', '1825 N 38th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/KEIM', 'KEIM', '1');
CALL updateOrInsertLocation('Entomology Hall', '1700 East Campus Mall', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/ENTO', 'ENTO', '1');
CALL updateOrInsertLocation('Mussehl Hall', '1915 N 38th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/MUSH', 'MUSH', '1');
CALL updateOrInsertLocation('Love Memorial Hall', '3420 Holdrege St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/LRH', 'LRH', '1');
CALL updateOrInsertLocation('Conservation & Survey Annex', '2000 N 34th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CSA', 'CSA', '1');
CALL updateOrInsertLocation('Service Building', '1915 N 37th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/SVC', 'SVC', '1');
CALL updateOrInsertLocation('Larsen Tractor Museum', '1925 N 37th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/LTM', 'LTM', '1');
CALL updateOrInsertLocation('Forestry Hall', '1800 N 37th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/FORS', 'FORS', '1');
CALL updateOrInsertLocation('Water Sciences Laboratory', '1840 N 37th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/WL', 'WL', '1');
CALL updateOrInsertLocation('Hardin Hall', '3310 Holdrege St', 'Lincoln', 'NE', '68583', 'https://maps.unl.edu/HARH', 'HARH', '1');
CALL updateOrInsertLocation('Theodore Jorgensen Hall', '855 N 16th St', 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/JH', 'JH', '1');
CALL updateOrInsertLocation('Forage Research Laboratory - USDA', '3870 Center Dr', 'Lincoln', 'NE', '68510', 'https://maps.unl.edu/FORL', 'FORL', '1');
CALL updateOrInsertLocation('Food Industry Complex', '3720 East Campus Loop S', 'Lincoln', 'NE', '68583', 'https://maps.unl.edu/FOOD', 'FOOD', '1');
CALL updateOrInsertLocation('Filley Hall', '3720 East Campus Loop S', 'Lincoln', 'NE', '68583', 'https://maps.unl.edu/FYH', 'FYH', '1');
CALL updateOrInsertLocation('Colonial Terrace Apartment A-1', '3330 Starr St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTA1', 'CTA1', '1');
CALL updateOrInsertLocation('Colonial Terrace Apartment A-2', '3400 Starr St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTA2', 'CTA2', '1');
CALL updateOrInsertLocation('Colonial Terrace Apartment A-3', '3323 Starr St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTA3', 'CTA3', '1');
CALL updateOrInsertLocation('Colonial Terrace Apartment A-4', '3401 Starr St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTA4', 'CTA4', '1');
CALL updateOrInsertLocation('Colonial Terrace Apartment B', '3344 Starr St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTB', 'CTB', '1');
CALL updateOrInsertLocation('Colonial Terrace Apartment C-1', '3320 Starr St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTC1', 'CTC1', '1');
CALL updateOrInsertLocation('Colonial Terrace Apartment C-2', '3340 Starr St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTC2', 'CTC2', '1');
CALL updateOrInsertLocation('Colonial Terrace Apartment C-3', '3315 Starr St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTC3', 'CTC3', '1');
CALL updateOrInsertLocation('Richards Hall', '560 Stadium Dr', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/RH', 'RH', '1');
CALL updateOrInsertLocation('Baker Hall', '1830 N 38th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/MBH', 'MBH', '1');
CALL updateOrInsertLocation('National Agroforestry Center - USDA', '1945 N 38th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/NAC', 'NAC', '1');
CALL updateOrInsertLocation('Stewart Seed Laboratory', '2101 N 38th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/SSL', 'SSL', '1');
CALL updateOrInsertLocation('Bio-Fiber Development Laboratory', '1605 N 35th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/BDL', 'BDL', '1');
CALL updateOrInsertLocation('Ruth Staples Laboratory', '1855 N 35th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CDL', 'CDL', '1');
CALL updateOrInsertLocation('Warehouse 1', '3630 East Campus Loop N', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/W1', 'W1', '1');
CALL updateOrInsertLocation('Terry M. Carpenter Telecommunications Center', '1800 N 33rd St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/TELC', 'TELC', '1');
CALL updateOrInsertLocation('Warehouse 2', '2105 N 38th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/W2', 'W2', '1');
CALL updateOrInsertLocation('Varner Hall', '3835 Holdrege St', 'Lincoln', 'NE', '68583', 'https://maps.unl.edu/VARH', 'VARH', '1');
CALL updateOrInsertLocation('Nebraska East Union', '1705 Arbor Dr', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/NEU', 'NEU', '1');
CALL updateOrInsertLocation('Agronomy & Horticulture Greenhouse 4', '3855 Merrill St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/AHG4', 'AHG4', '1');
CALL updateOrInsertLocation('Barkley Memorial Center', '4075 East Campus Loop S', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/BKC', 'BKC', '1');
CALL updateOrInsertLocation('VBS Annex', '1900 N 42nd St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/VDC', 'VDC', '1');
CALL updateOrInsertLocation('Veterinary Medicine and Biomedical Sciences Hall', '1880 N 42nd St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/VBS', 'VBS', '1');
CALL updateOrInsertLocation('Veterinary Clinical Skills Laboratory', '2000 N 43rd St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/VCSL', 'VCSL', '1');
CALL updateOrInsertLocation('Plant Sciences Hall', '1875 N 38th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/PLSH', 'PLSH', '1');
CALL updateOrInsertLocation('Abel-Sandoz Welcome Center', '830 N 17th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ASWC', 'ASWC', '1');
CALL updateOrInsertLocation('Splinter Laboratories', '2000 N 35th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/SPL', 'SPL', '1');
CALL updateOrInsertLocation('Sewage Sterilization Plant', '2005 N 43rd St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/SEW', 'SEW', '1');
CALL updateOrInsertLocation('National Agroforestry Center Storage Building - USDA', '2140 N 38th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/NAST', 'NAST', '1');
CALL updateOrInsertLocation('Animal Science Complex', '3940 Fair St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/ANSC', 'ANSC', '1');
CALL updateOrInsertLocation('Welpton Courtroom Building', '1875 N 42nd St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/WELC', 'WELC', '1');
CALL updateOrInsertLocation('Agronomy & Horticulture Outstate Testing Laboratory', '3720 Merrill St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/AHTL', 'AHTL', '1');
CALL updateOrInsertLocation('Landscape Services East Campus', '3520 East Campus Loop N', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/LSEC', 'LSEC', '1');
CALL updateOrInsertLocation('Nebraska Statewide Arboretum Greenhouse', '2150 N 38th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/NSAG', 'NSAG', '1');
CALL updateOrInsertLocation('Perin Porch', '3621 East Campus Loop S', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/PER', 'PER', '1');
CALL updateOrInsertLocation('Agronomy & Horticulture/Forestry Shops', '2103 N 38th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/AHFS', 'AHFS', '1');
CALL updateOrInsertLocation('International Quilt Museum', '1523 N 33rd St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/IQM', 'IQM', '1');
CALL updateOrInsertLocation('Community Garden Shed', '4401 Fair St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/GRDN', 'GRDN', '1');
CALL updateOrInsertLocation('Colonial Terrace Apartment C-4', '3333 Starr St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTC4', 'CTC4', '1');
CALL updateOrInsertLocation('Colonial Terrace Apartment D-1', '3301 Starr St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTD1', 'CTD1', '1');
CALL updateOrInsertLocation('Colonial Terrace Apartment D-2', '3345 Starr St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTD2', 'CTD2', '1');
CALL updateOrInsertLocation('Colonial Terrace Apartments Shop 1', '3332 Starr St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTS1', 'CTS1', '1');
CALL updateOrInsertLocation('Colonial Terrace Apartments Shop 2', '3342 Starr St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CTS2', 'CTS2', '1');
CALL updateOrInsertLocation('Pershing Maintenance', '2000 N 33rd St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/PM', 'PM', '1');
CALL updateOrInsertLocation('Agronomy & Horticulture Greenhouse 1', '2100 N 38th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/AHG1', 'AHG1', '1');
CALL updateOrInsertLocation('Entomology Greenhouse 2', '2110 N 38th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/EGR2', 'EGR2', '1');
CALL updateOrInsertLocation('Entomology Greenhouse 3', '2120 N 38th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/EGR3', 'EGR3', '1');
CALL updateOrInsertLocation('Agronomy & Horticulture Greenhouse 2', '2041 N 38th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/AHG2', 'AHG2', '1');
CALL updateOrInsertLocation('Natural Resources Research Annex', '2051 N 38th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/NRRA', 'NRRA', '1');
CALL updateOrInsertLocation('Sapp Recreation Facility', '841 N 14th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/CREC', 'CREC', '1');
CALL updateOrInsertLocation('Canfield Administration Building South', '501 N 14th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ADMS', 'ADMS', '1');
CALL updateOrInsertLocation('Andrews Hall', '625 N 14th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ANDR', 'ANDR', '1');
CALL updateOrInsertLocation('Architecture Hall', '402 Stadium Dr', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ARCH', 'ARCH', '1');
CALL updateOrInsertLocation('Avery Hall', '1144 T St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/AVH', 'AVH', '1');
CALL updateOrInsertLocation('Bessey Hall', '1215 U St', 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/BESY', 'BESY', '1');
CALL updateOrInsertLocation('Brace Laboratory', '510 Stadium Dr', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/BL', 'BL', '1');
CALL updateOrInsertLocation('Burnett Hall', '1220 T St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/BURN', 'BURN', '1');
CALL updateOrInsertLocation('Coliseum', '1350 Vine St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/COL', 'COL', '1');
CALL updateOrInsertLocation('Love Library South', '1248 R St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/LLS', 'LLS', '1');
CALL updateOrInsertLocation('Love Library North & Link', '1300 R St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/LLN', 'LLN', '1');
CALL updateOrInsertLocation('Architecture Hall West', '400 Stadium Dr', 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/ARCW', 'ARCW', '1');
CALL updateOrInsertLocation('Pershing Military & Naval Science Building', '1360 Vine St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/M&N', 'M&N', '1');
CALL updateOrInsertLocation('Morrill Hall', '1335 U St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/MORR', 'MORR', '1');
CALL updateOrInsertLocation('Mueller Tower', '1307 U St', 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/MUEL', 'MUEL', '1');
CALL updateOrInsertLocation('Nebraska Hall', '900 N 16th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/NH', 'NH', '1');
CALL updateOrInsertLocation('Woods Art Building', '1140 R St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/WAB', 'WAB', '1');
CALL updateOrInsertLocation('Nebraska Union', '1400 R St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/NU', 'NU', '1');
CALL updateOrInsertLocation('Seaton Hall', '1525 U St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SEH', 'SEH', '1');
CALL updateOrInsertLocation('Benton Hall', '1535 U St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/BENH', 'BENH', '1');
CALL updateOrInsertLocation('Fairfield Hall', '1545 U St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/FAIR', 'FAIR', '1');
CALL updateOrInsertLocation('Selleck Quad Building K', '600 N 15th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SELK', 'SELK', '1');
CALL updateOrInsertLocation('Sheldon Museum of Art', '451 N 12th St', 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/SHEL', 'SHEL', '1');
CALL updateOrInsertLocation('Louise Pound Hall', '512 N 12th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/LPH', 'LPH', '1');
CALL updateOrInsertLocation('Stadium East', '1100 T St', 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/STE', 'STE', '1');
CALL updateOrInsertLocation('Stadium West', '1100 T St', 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/STW', 'STW', '1');
CALL updateOrInsertLocation('Canfield Administration Building North', '503 N 14th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ADMN', 'ADMN', '1');
CALL updateOrInsertLocation('Temple Building', '1209 R St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/TEMP', 'TEMP', '1');
CALL updateOrInsertLocation('University Health Center', '1500 U St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/UHC', 'UHC', '1');
CALL updateOrInsertLocation('Henzlik Hall', '1430 Vine St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/HENZ', 'HENZ', '1');
CALL updateOrInsertLocation('Westbrook Music Building', '1104 R St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/WMB', 'WMB', '1');
CALL updateOrInsertLocation('Behlen Laboratory', '500 Stadium Dr', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/BEL', 'BEL', '1');
CALL updateOrInsertLocation('Abel Hall', '880 N 17th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ARH', 'ARH', '1');
CALL updateOrInsertLocation('Sandoz Hall', '820 N 17th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SZRH', 'SZRH', '1');
CALL updateOrInsertLocation('Abel-Sandoz Food Service Building', '840 N 17th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ASFS', 'ASFS', '1');
CALL updateOrInsertLocation('501 Building', '501 Stadium Dr', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/501', '501', '1');
CALL updateOrInsertLocation('Hamilton Hall', '639 N 12th St', 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/HAH', 'HAH', '1');
CALL updateOrInsertLocation('Oldfather Hall', '660 N 12th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/OLDH', 'OLDH', '1');
CALL updateOrInsertLocation('Business Services Complex', '1700 Y St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/BSC', 'BSC', '1');
CALL updateOrInsertLocation('Kimball Recital Hall', '1113 R St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/KRH', 'KRH', '1');
CALL updateOrInsertLocation('Manter Hall', '1101 T St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/MANT', 'MANT', '1');
CALL updateOrInsertLocation('Schorr Center', '1100 T St', 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/SHOR', 'SHOR', '1');
CALL updateOrInsertLocation('Campus Recreation Boat House', '1000 N 16th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/BOAT', 'BOAT', '1');
CALL updateOrInsertLocation('Watson Building', '1309 N 17th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/WAT', 'WAT', '1');
CALL updateOrInsertLocation('Wick Alumni Center', '1520 R St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/WICK', 'WICK', '1');
CALL updateOrInsertLocation('Ice Box', '1880 Transformation Dr', 'Lincoln', 'NE', '68501', 'https://maps.unl.edu/ICBX', 'ICBX', '1');
CALL updateOrInsertLocation('Lied Center for Performing Arts', '301 N 12th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/LIED', 'LIED', '1');
CALL updateOrInsertLocation('Prem S. Paul Research Center at Whittier School', '2200 Vine St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/WHIT', 'WHIT', '1');
CALL updateOrInsertLocation('19th and Vine Parking Garage', '1830 Vine St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/19PG', '19PG', '1');
CALL updateOrInsertLocation('Architecture Hall Link', '404 Stadium Dr', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ARCL', 'ARCL', '1');
CALL updateOrInsertLocation('Cook Pavilion', '845 N 14th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/COOK', 'COOK', '1');
CALL updateOrInsertLocation('Husker Hall', '705 N 23rd St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/HUSK', 'HUSK', '1');
CALL updateOrInsertLocation('Beadle Center', '1901 Vine St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/BEAD', 'BEAD', '1');
CALL updateOrInsertLocation('Bioscience Greenhouses', '1901 Vine St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/BIOG', 'BIOG', '1');
CALL updateOrInsertLocation('Facilities Management & Planning', '1901 Y St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/FMP', 'FMP', '1');
CALL updateOrInsertLocation('Facilities Management C', '1901 Y St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/FMC', 'FMC', '1');
CALL updateOrInsertLocation('Facilities Management D', '1901 Y St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/FMD', 'FMD', '1');
CALL updateOrInsertLocation('Facilities Management E', '1901 Y St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/FME', 'FME', '1');
CALL updateOrInsertLocation('Facilities Management F', '1901 Y St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/FMF', 'FMF', '1');
CALL updateOrInsertLocation('Alexander Building', '1410 Q St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ALEX', 'ALEX', '1');
CALL updateOrInsertLocation('U Street Apartments', '2224 U St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/UST', 'UST', '1');
CALL updateOrInsertLocation('Vine Street Apartments West', '2222 Vine St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/2222', '2222', '1');
CALL updateOrInsertLocation('Vine Street Apartments East', '2244 Vine St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/2244', '2244', '1');
CALL updateOrInsertLocation('Stadium Drive Parking Garage', '625 Stadium Dr', 'Lincoln', 'NE', '68501', 'https://maps.unl.edu/SDPG', 'SDPG', '1');
CALL updateOrInsertLocation('Andersen Hall', '200 Centennial Mall N', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ANDN', 'ANDN', '1');
CALL updateOrInsertLocation('Teachers College Hall', '1400 Vine St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/TEAC', 'TEAC', '1');
CALL updateOrInsertLocation('Kauffman Academic Residential Center', '630 N 14th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/KAUF', 'KAUF', '1');
CALL updateOrInsertLocation('Landscape Services Metal Canopy', '1340 N 17th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/LSMC', 'LSMC', '1');
CALL updateOrInsertLocation('Othmer Hall', '820 N 16th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/OTHM', 'OTHM', '1');
CALL updateOrInsertLocation('Mary Riepma Ross Media Arts Center-Van Brunt Visitors Center', '313 N 13th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/RVB', 'RVB', '1');
CALL updateOrInsertLocation('17th & R Parking Garage', '300 N 17th St', 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/17PG', '17PG', '1');
CALL updateOrInsertLocation('Nebraska Champions Club', '707 Stadium Dr', 'Lincoln', 'NE', '68501', 'https://maps.unl.edu/NECH', 'NECH', '1');
CALL updateOrInsertLocation('The Courtyards', '733 N 17th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/CORT', 'CORT', '1');
CALL updateOrInsertLocation('14th & Avery Parking Garage', '1111 N 14th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/14PG', '14PG', '1');
CALL updateOrInsertLocation('Transportation Services', '1931 N Antelope Valley Pky', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/TRAN', 'TRAN', '1');
CALL updateOrInsertLocation('The Village', '1055 N 16th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/VILL', 'VILL', '1');
CALL updateOrInsertLocation('Facilities Management Shops', '942 N 22nd St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/FMS', 'FMS', '1');
CALL updateOrInsertLocation('Osborne Athletic Complex', '800 Stadium Dr', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/MSTD', 'MSTD', '1');
CALL updateOrInsertLocation('Hawks Championship Center', '1111 Salt Creek Rdwy', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/HCC', 'HCC', '1');
CALL updateOrInsertLocation('Selleck Quad Building D', '600 N 15th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SELD', 'SELD', '1');
CALL updateOrInsertLocation('Selleck Quad Building E', '600 N 15th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SELE', 'SELE', '1');
CALL updateOrInsertLocation('Selleck Quad Building F', '600 N 15th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SELF', 'SELF', '1');
CALL updateOrInsertLocation('Selleck Quad Building G', '600 N 15th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SELG', 'SELG', '1');
CALL updateOrInsertLocation('Selleck Quad Building H', '600 N 15th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SELH', 'SELH', '1');
CALL updateOrInsertLocation('Selleck Quad Building J', '600 N 15th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SELJ', 'SELJ', '1');
CALL updateOrInsertLocation('Selleck Quad Building L - Food Service', '600 N 15th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SELL', 'SELL', '1');
CALL updateOrInsertLocation('Stadium North', '1100 T St', 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/STNO', 'STNO', '1');
CALL updateOrInsertLocation('Stadium South', '1100 T St', 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/STSO', 'STSO', '1');
CALL updateOrInsertLocation('Harper Hall', '1150 N 14th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/HRH', 'HRH', '1');
CALL updateOrInsertLocation('Schramm Hall', '1130 N 14th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SCRH', 'SCRH', '1');
CALL updateOrInsertLocation('1101 Y', '1101 Y St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/YS1', 'YS1', '1');
CALL updateOrInsertLocation('Smith Hall', '1120 N 14th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SMRH', 'SMRH', '1');
CALL updateOrInsertLocation('Harper Dining Center', '1140 N 14th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/HDC', 'HDC', '1');
CALL updateOrInsertLocation('2511 Kimco Court A', '2511 Kimco Ct', 'Lincoln', 'NE', '68521', 'https://maps.unl.edu/L014', 'L014', '1');
CALL updateOrInsertLocation('Fleming Fields Park Concessions Bldg', '3233 Huntington Ave', 'Lincoln', 'NE', '68504', 'https://maps.unl.edu/FFCB', 'FFCB', '1');
CALL updateOrInsertLocation('Campus Rec Equipment Building 3 - Whittier Fields', '2251 W St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/ORB3', 'ORB3', '1');
CALL updateOrInsertLocation('Hewit Place', '1155 Q St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/HEWP', 'HEWP', '1');
CALL updateOrInsertLocation('UNL Children''s Center', '2225 W St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/CHC', 'CHC', '1');
CALL updateOrInsertLocation('Bus Garage', '1935 N Antelope Valley Pky', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/BUSG', 'BUSG', '1');
CALL updateOrInsertLocation('Jackie Gaughan Multicultural Center', '1505 S St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/GAUN', 'GAUN', '1');
CALL updateOrInsertLocation('Landscape Services Equipment Building', '3620 East Campus Loop N', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/LSEB', 'LSEB', '1');
CALL updateOrInsertLocation('East Thermal Energy Storage', '3755 Merrill St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/ETES', 'ETES', '1');
CALL updateOrInsertLocation('Fleming Fields Annex Building', '2301 N 33rd St', 'Lincoln', 'NE', '68504', 'https://maps.unl.edu/FFAB', 'FFAB', '1');
CALL updateOrInsertLocation('Utility Plant, City Campus', '905 N 14th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/CCUP', 'CCUP', '1');
CALL updateOrInsertLocation('North Building 2', '1350 Military Rd', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/NB2', 'NB2', '1');
CALL updateOrInsertLocation('North Building 1', '1300 Military Rd', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/NB1', 'NB1', '1');
CALL updateOrInsertLocation('Recycling and Refuse Building', '1311 Military Rd', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/RRB', 'RRB', '1');
CALL updateOrInsertLocation('ITS Annex', '1321 Military Rd', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ITSA', 'ITSA', '1');
CALL updateOrInsertLocation('18th & R Parking Garage', '1801 R St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/18R', '18R', '1');
CALL updateOrInsertLocation('Facilities Implement Building', '1330 N 17th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/FIB', 'FIB', '1');
CALL updateOrInsertLocation('NEMA Building', '1360 Military Rd', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/NEMA', 'NEMA', '1');
CALL updateOrInsertLocation('Documents Facility', '1331 Military Rd', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/DF', 'DF', '1');
CALL updateOrInsertLocation('Alex Gordon Training Complex', 'Line Dr', 'Lincoln', 'NE', '', 'https://maps.unl.edu/L045', 'L045', '1');
CALL updateOrInsertLocation('Pinnacle Bank Arena', '400 Pinnacle Arena Dr', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/PBA', 'PBA', '1');
CALL updateOrInsertLocation('Outdoor Adventures Center', '930 N 14th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/OAC', 'OAC', '1');
CALL updateOrInsertLocation('Campus Renewable Energy System Building', '2402 Salt Creek Rdwy', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/CRES', 'CRES', '1');
CALL updateOrInsertLocation('Recreation and Wellness Center', '1717 N 35th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/RWC', 'RWC', '1');
CALL updateOrInsertLocation('Morrison Center', '4240 Fair St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/MOLR', 'MOLR', '1');
CALL updateOrInsertLocation('Eastside Suites', '433 N 19th St', 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/ESST', 'ESST', '1');
CALL updateOrInsertLocation('Sid and Hazel Dillon Tennis Center', '2400 N Antelope Valley Pky', 'Lincoln', 'NE', '68521', 'https://maps.unl.edu/DTC', 'DTC', '1');
CALL updateOrInsertLocation('Maintenance Storage Building', '1910 N Antelope Valley Pky', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/MSB', 'MSB', '1');
CALL updateOrInsertLocation('Breslow Ice Center', '433 V St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/BIC', 'BIC', '1');
CALL updateOrInsertLocation('Material Handling Facility', '3700 Merrill St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/MHF', 'MHF', '1');
CALL updateOrInsertLocation('Howard L. Hawks Hall', '730 N 14th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/HLH', 'HLH', '1');
CALL updateOrInsertLocation('Mabel Lee Fields IPC', '1433 W St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/MIPC', 'MIPC', '1');
CALL updateOrInsertLocation('Campus Rec Equipment Building 7 - Mabel Lee Fields', '1433 W St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ORB7', 'ORB7', '1');
CALL updateOrInsertLocation('Greenhouse Innovation Center', '1920 N 21st St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ICG', 'ICG', '1');
CALL updateOrInsertLocation('Food Innovation Center', '1901 N 21st St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/FIC', 'FIC', '1');
CALL updateOrInsertLocation('Innovation Commons Conference Center', '2021 Transformation Dr', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ICC', 'ICC', '1');
CALL updateOrInsertLocation('Fluid Cooler Building', '520 N 17th St', 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/FCB', 'FCB', '1');
CALL updateOrInsertLocation('Nebraska Veterinary Diagnostic Center', '4040 East Campus Loop N', 'Lincoln', 'NE', '68583', 'https://maps.unl.edu/NVDC', 'NVDC', '1');
CALL updateOrInsertLocation('Landscape Implement Building', '1320 N 17th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/LIB', 'LIB', '1');
CALL updateOrInsertLocation('McCollum Hall', '1875 N 42nd St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/LAW', 'LAW', '1');
CALL updateOrInsertLocation('Willa S. Cather Dining Complex', '530 N 17th St', 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/WCDC', 'WCDC', '1');
CALL updateOrInsertLocation('1217 Q St', '1217 Q St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/L055', 'L055', '1');
CALL updateOrInsertLocation('Utility Response Facility', '3730 Merrill St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/UTF', 'UTF', '1');
CALL updateOrInsertLocation('City Thermal Energy Storage', '1340 N 17th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/CTES', 'CTES', '1');
CALL updateOrInsertLocation('University Health Center and College of Nursing', '550 N 19th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/HCCN', 'HCCN', '1');
CALL updateOrInsertLocation('Library Depository Retrieval Facility', '2055 N 35th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/LDR', 'LDR', '1');
CALL updateOrInsertLocation('Massengale Residential Center', '1710 Arbor Dr', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/MRC', 'MRC', '1');
CALL updateOrInsertLocation('Orchard House Replacement', '4417 Fair St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/OHR', 'OHR', '1');
CALL updateOrInsertLocation('18th & S Support Building', '510 N 17th St', 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/18S', '18S', '1');
CALL updateOrInsertLocation('University Suites', '1780 R St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/UNST', 'UNST', '1');
CALL updateOrInsertLocation('The Robert E. Knoll Residential Center', '440 N 17th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/KNOL', 'KNOL', '1');
CALL updateOrInsertLocation('College of Dentistry', '4000 East Campus Loop S', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/DENT', 'DENT', '1');
CALL updateOrInsertLocation('Plant Pathology Greenhouse', '2075 N 38th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/PPG', 'PPG', '1');
CALL updateOrInsertLocation('Agronomy & Horticulture Greenhouse 3', '2065 N 38th St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/AHG3', 'AHG3', '1');
CALL updateOrInsertLocation('Devaney Sports Center', '1600 Court St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/DEV', 'DEV', '1');
CALL updateOrInsertLocation('Johnny Carson Center for Emerging Media Arts', '1300 Q St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/CEMA', 'CEMA', '1');
CALL updateOrInsertLocation('Haymarket Park Softball Stadium Complex', '400 Line Drive Cir', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/HAYS', 'HAYS', '1');
CALL updateOrInsertLocation('Haymarket Park Baseball Stadium Complex', '403 Line Drive Cir', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/HAYB', 'HAYB', '1');
CALL updateOrInsertLocation('Agronomy & Horticulture Physiology Building', '3710 Merrill St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/AHPH', 'AHPH', '1');
CALL updateOrInsertLocation('USDA Physiology Building - USDA', '3708 Merrill St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/USDP', 'USDP', '1');
CALL updateOrInsertLocation('Materials Management Facility', '3735 Merrill St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/MMF', 'MMF', '1');
CALL updateOrInsertLocation('Facilities Management G', '1901 Y St', 'Lincoln', 'NE', '68503', 'https://maps.unl.edu/FMG', 'FMG', '1');
CALL updateOrInsertLocation('Scott Engineering Center', '844 N 16th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SEC', 'SEC', '1');
CALL updateOrInsertLocation('Engineering Research Center', '880 N 16th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ERC', 'ERC', '1');
CALL updateOrInsertLocation('Carolyn Pope Edwards Hall', '840 N 14th St', 'Lincoln', 'NE', '68588', 'https://maps.unl.edu/CPEH', 'CPEH', '1');
CALL updateOrInsertLocation('The Rise Building', '2125 Transformation Dr', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/RISE', 'RISE', '1');
CALL updateOrInsertLocation('The Scarlet Hotel', '2101 Transformation Dr', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/SCAR', 'SCAR', '1');
CALL updateOrInsertLocation('Gwendolyn A. Newkirk Human Sciences Building', '1650 N 35th St', 'Lincoln', 'NE', '68583', 'https://maps.unl.edu/GNHS', 'GNHS', '1');
CALL updateOrInsertLocation('Neihardt Center', '540 N 16th St', 'Lincoln', 'NE', '68508', 'https://maps.unl.edu/ ', ' ', '1');

COMMIT;

DROP PROCEDURE updateOrInsertLocation;
