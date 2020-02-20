<?php

namespace Application\Models\Gateways\Parser;

use Application\Models\Proxies\FileProxy;
use XmlWriter;

class BurningGlassJobsParser
{
    /**
     * Get a specific key from XML struct
     * @return array
     */
    public static function parseIndexValues($desiredKey, $indexes, $values)
    {
        $return = array();

        foreach ($indexes as $key => $value) {
            if ($key == $desiredKey) {
                $ranges = $value;

                for ($i=0; $i < count($ranges); $i+=2) {
                    $offset = $ranges[$i] + 1;
                    $length = $ranges[$i + 1] - $offset;

                    $job_values = array_slice($values, $offset, $length);
                    $job = array();

                    for ($j=0; $j < count($job_values); $j++) {
                        $job[$job_values[$j]['tag']] = (!empty($job_values[$j]['value'])) ? $job_values[$j]['value'] : "";
                    }

                    $return[] = $job;
                }
            } else {
                continue;
            }
        }

        return $return;
    }

    /**
     * Convert BurningGlass "Add" file to associative array
     * @return array
     */
    public static function addFileToArray(FileProxy $xmlFile)
    {
        $parser = xml_parser_create();
        $values = array();
        $indexes = array();

        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $xmlFile->getContents(), $values, $indexes);
        xml_parser_free($parser);

        return self::parseIndexValues('Job', $indexes, $values);
    }

    /**
     * Convert array to BurningGlass "Add" file
     * @return Application\Models\Proxies\FileProxy
     */
    public static function addFileFromArray(array $jobs)
    {
        $writer = new XmlWriter;
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->setIndentString('  ');
        $writer->startElement('Jobs');

        foreach ($jobs as $jobData) {
            $writer->startElement('Job');

            foreach ($jobData as $key => $value) {
                $writer->startElement($key);
                $writer->text($value);
                $writer->endElement();
            }

            $writer->endElement();
        }

        $writer->endElement();

        return $writer->flush();
    }

    /**
     * Convert BurningGlass "Add" file to associative array
     * @return array
     */
    public static function removeFileToArray(FileProxy $xmlFile)
    {
        $parser = xml_parser_create();
        $values = array();
        $indexes = array();

        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $xmlFile->getContents(), $values, $indexes);
        xml_parser_free($parser);

        return self::parseIndexValues('row', $indexes, $values);
    }

    public static function removeFileFromArray(array $jobs)
    {
        $writer = new XmlWriter;
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->setIndentString('  ');
        $writer->startElement('expiredjobs');

        foreach ($jobs as $job) {
            $writer->startElement('row');

            foreach ($job as $key => $jobData) {
                $writer->startElement($key);
                $writer->text($jobData);
                $writer->endElement();
            }

            $writer->endElement();
        }

        $writer->endElement();

        return $writer->flush();
    }
}