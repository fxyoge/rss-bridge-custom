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

        if (!$html) {
            Debug::log("Failed to retrieve the HTML document.");
            return;
        }

        $html = defaultLinkTo($html, self::URI);

        $months = $html->find('div.MainDivClass p');

        if (empty($months)) {
            Debug::log("No months found in the HTML document.");
            return;
        }

        foreach ($months as $month) {
            if (preg_match('/^[A-Z]{3,10} \d{4}$/', $month->plaintext)) {
                $date = $month->plaintext;
                Debug::log("Processing date: $date\n");

                $entries = $month->next_sibling();

                while ($entries && $entries->tag === 'p') {
                    $title = $entries->find('p.title2', 0);
                    if ($title) {
                        $titleText = $title->plaintext;
                        Debug::log("Found title: $titleText");

                        $content = $entries->find('p.para2', 0);
                        if ($content) {
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
                        } else {
                            Debug::log("No content found for title: $titleText");
                        }
                    }
                    $entries = $entries->next_sibling();
                }
            } else {
                Debug::log("No matching date format found: " . $month->plaintext);
            }
        }
    }
}
