<?php

class RouterSecurityBridge extends BridgeAbstract
{
    const MAINTAINER = 'fxyoge';
    const NAME = 'Router Security Bugs';
    const URI = 'https://routersecurity.org/bugs.php';
    const CACHE_TIMEOUT = 300; // 5 minutes
    const DESCRIPTION = 'Returns the latest router security bugs';

    public function collectData()
    {
        $html = getSimpleHTMLDOM(self::URI);

        $html = defaultLinkTo($html, self::URI);

        $months = $html->find('div.MainDivClass p');

        foreach ($months as $month) {
            if (preg_match('/^[A-Z]{3,10} \d{4}$/', $month->plaintext)) {
                $date = $month->plaintext;

                $entries = $month->next_sibling();

                while ($entries && $entries->tag === 'p') {
                    $title = $entries->find('p.title2', 0);
                    if ($title) {
                        $titleText = $title->plaintext;

                        $content = $entries->find('p.para2', 0);
                        $link = $content->find('a', 0)->href;
                        $contentText = $content->innertext;

                        $dateText = $content->find('br', 1)->next_sibling()->plaintext;
                        $dateFormatted = DateTime::createFromFormat('F d, Y', trim($date . ' ' . $dateText))->format('Y-m-d H:i:s');

                        $item = [];
                        $item['title'] = $titleText;
                        $item['uri'] = $link;
                        $item['timestamp'] = strtotime($dateFormatted);
                        $item['content'] = $contentText;

                        $this->items[] = $item;
                    }
                    $entries = $entries->next_sibling();
                }
            }
        }
    }
}
