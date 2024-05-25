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
        $html = getSimpleHTMLDOM($this->getURI() . '?index.php');

        $html = defaultLinkTo($html, self::URI);

        $entries = $html->find('div#leftDDiv div.divisionalDiv');

        foreach ($entries as $entry) {
            $title = $entry->find('h1 a', 0)->plaintext;
            $link = $entry->find('h1 a', 0)->href;
            
            $date = $entry->find('p', 0)->plaintext;
            $date = str_replace(' at', '', $date);
            $date = DateTime::createFromFormat('F j, Y g:i A', $date)->format('Y-m-d H:i:s');

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
