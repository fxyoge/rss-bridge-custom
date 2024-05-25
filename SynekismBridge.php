<?php

class SynekismBridge extends BridgeAbstract
{
    const MAINTAINER = 'fxyoge';
    const NAME = 'Synekism Blog';
    const URI = 'https://www.synekism.com/';
    const CACHE_TIMEOUT = 300; //5 min
    const DESCRIPTION = 'Returns the Synekism news';
    const PARAMETERS = [];

    public function collectData()
    {
        $html = file_get_html('https://www.synekism.com/index.php');

        $entries = $html->find('div#leftDDiv div.divisionalDiv');

        foreach ($entries as $entry) {
            $title = $entry->find('h1 a', 0)->plaintext;
            $link = $entry->find('h1 a', 0)->href;
            $date = $entry->find('p', 0)->plaintext;
            $content = $entry->find('p', 1)->innertext;

            $item = array();
            $item['title'] = $title;
            $item['uri'] = 'https://www.synekism.com/' . $link;
            $item['timestamp'] = strtotime($date);
            $item['content'] = $content;

            $this->items[] = $item;
        }
    }
}
