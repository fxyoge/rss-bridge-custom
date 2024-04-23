<?php

class ConfettoBridge extends BridgeAbstract
{
    const MAINTAINER = 'fxyoge';
    const NAME = 'Confetto Blog';
    const URI = 'https://nanahira.jp/news.html';
    const CACHE_TIMEOUT = 300; //5 min
    const DESCRIPTION = 'Returns Nanahira\'s news';
    const PARAMETERS = [];

    public function collectData()
    {
        $html = getSimpleHTMLDOM($this->getURI());

        $html = defaultLinkTo($html, self::URI);

        foreach ($html->find('div.act-item') as $element) {
            $item = [];

            $articleThumb = $element->find('a', 0);
            $articleImage = $articleThumb->find('img.act-img', 0);
            $articleTitleBar = $element->find('div.act-head-title', 0);
            $articleDate = $articleTitleBar->find('p', 0);
            $articleTitle = $articleTitleBar->find('a', 0);
            $articleBody = $element->find('p.act-body', 0);

            $item['uri'] = $articleThumb->href;
            $imageSrc = $this->getURI() . '/' . ($articleImage->src);
            $item['title'] = trim($articleTitle->innertext);
            $item['timestamp'] = str_replace('.', '-', $articleDate->innertext);

            $content = '<div>';
            if ($articleImage) {
                $content .= '<img src="' . $imageSrc . '" alt="thumbnail">';
            }
            if ($articleBody) {
                $content .= '<p>' . $articleBody->innertext . '</p>';
            }
            $content .= '</div>';

            $item['content'] = $content;

            $this->items[] = $item;
        }
    }
}
