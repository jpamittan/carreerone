<?php

namespace App\Models\Services;;

trait XMLToArray {
    protected function XMLToArray($xml)  {
        if (empty($xml)){
        	return [];
        }
        $doc = new \DOMDocument();
        $doc->loadXML($xml);
        return $this->DOMToArray($doc->documentElement);
    }

    protected function DOMToArray($node) {
        $output = [];
        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:
                $output = trim($node->textContent);
            break;
            case XML_ELEMENT_NODE:
                for ($i = 0, $m = $node->childNodes->length; $i < $m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = $this->DOMToArray($child);
                    if (isset($child->tagName)) {
                        $t = $child->tagName;
                        if (! isset($output[$t])) {
                            $output[$t] = [];
                        }
                        $output[$t][] = $v;
                        if ($t == 'DateActive') {
                            $a = [];
                            foreach($child->attributes as $attrName => $attrNode) {
                                $a[$attrName] = (string) $attrNode->value;
                            }
                            $output['DateActiveUTC'] = $a['Date'];
                        }
                    } else if ($v) {
                        $output = (string) $v;
                    }
                }
                if (is_array($output) || $node->attributes->length) {
                    if($node->attributes->length) {
                        $a = [];
                        foreach($node->attributes as $attrName => $attrNode) {
                            $a[$attrName] = (string) $attrNode->value;
                        }
                        $output = (array) $output;
                        $output['@attributes'] = $a;
                    }
                    foreach ($output as $t => $v) {
                        if (is_array($v) && count($v) == 1 && $t != '@attributes') {
                            $output[$t] = $v[0];
                        }
                    }
                    if (empty($output)) {
                        $output = '';
                    }
                }
            break;
        }
        return $output;
    }
}
