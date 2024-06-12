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

        $contentDiv = $html->find('div.MainDivClass', 0);

        if (!$contentDiv) {
            Debug::log("Content div not found.");
            return;
        }

        $currentMonth = null;

        foreach ($contentDiv->children as $element) {
            if ($element->tag === 'p' && preg_match('/^[A-Z]{3,10} \d{4}$/', $element->plaintext)) {
                $currentMonth = $element->plaintext;
                Debug::log("Processing month: $currentMonth");
            } elseif ($currentMonth && $element->tag === 'p' && $element->class === 'title2') {
                $title = $element->plaintext;
                Debug::log("Found title: $title");

                $contentElement = $element->next_sibling();
                if ($contentElement && $contentElement->tag === 'p' && $contentElement->class === 'para2') {
                    $link = $contentElement->find('a', 0)->href;
                    $contentText = $contentElement->innertext;
                    preg_match('/[A-Z][a-z]+ \d{1,2}, \d{4}/', $contentText, $matches);
                    $dateText = $matches[0] ?? $currentMonth;
                    $dateFormatted = DateTime::createFromFormat('F j, Y', trim($dateText))->format('Y-m-d H:i:s');

                    $item = [];
                    $item['title'] = $title;
                    $item['uri'] = $link;
                    $item['timestamp'] = strtotime($dateFormatted);
                    $item['content'] = $contentText;

                    $this->items[] = $item;
                } else {
                    Debug::log("No content found for title: $title");
                }
            }
        }
    }
}
