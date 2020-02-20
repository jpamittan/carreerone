<?php

namespace App\Http\Controllers\site;
use App\Http\Controllers\Controller;

/**
 * Class StaticPageController
 * @package App\Http\Controllers\site
 */
class StaticPageController extends Controller {
    /**
     * @param $feedUrl
     * @return string
     */
    protected function loadFeedXMLFromUrl($feedUrl) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $feedUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $data = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);
        if (!empty($curlError)) {
            return "<div class='col-sm-12'><p>" . $curlError . "</p></div>";
        } else {
            return simplexml_load_string($data);
        }
    }

    public function getResources() {
        $feedUrl = !empty(config('static_page.resources.feed_url')) ? config('static_page.resources.feed_url') : "";
        $body = $this->loadFeedXMLFromUrl($feedUrl);
        try {
            $htmlBody = (string)$body->channel->item->children('content', true)->encoded[0];
            if (!empty($htmlBody)) {
                $htmlBody = str_replace("ezcol-three-fifth", "col-lg-7 col-sm-12 padding-10", $htmlBody);
                $htmlBody = str_replace("ezcol-two-fifth", "col-sm-4 col-sm-12 padding-10", $htmlBody);
                $body = $htmlBody;
            }
        } catch (\Exception $exception) {
            $body = view('common.errors.jumbotron', ['title' => 'Failed to load resources', 'body' => $exception->getMessage()]);
        }
        return view('site.static.resources', ['body' => $body]);
    }
}