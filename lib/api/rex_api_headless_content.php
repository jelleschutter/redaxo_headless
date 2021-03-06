<?php

class rex_api_headless_content extends rex_headless_api_base {

    function execute()
    {
        parent::execute();

        $path = rex_request('path', 'string', false);
        if ($path === false) {
            rex_response::setStatus(400);
            rex_response::sendJson([]);
            exit;
        }

        $domainName = rex_yrewrite::getHost();
        $domain = rex_yrewrite::getDomainByName($domainName);
        if ($domain === null) {
            $domain = rex_yrewrite::getDefaultDomain();
        }
        $articleMeta = rex_yrewrite::getArticleIdByUrl($domain, $path);

        if ($articleMeta === false) {
            rex_response::setStatus(400);
            rex_response::sendJson([]);
            exit;
        }

        $articleId = array_keys($articleMeta)[0];
        $articleClang = $articleMeta[$articleId];

        $seo = new rex_yrewrite_seo($articleId, $articleClang);

        $articleContent = new rex_article_content($articleId, $articleClang);

        rex_response::sendJson([
            'meta' => [
                'title' => $seo->getTitle(),
                'description' => $seo->getDescription()
            ],
            'title' => $articleContent->getValue('name'),
            'content' => $articleContent->getArticleTemplate()
        ]);
        exit;
    }
}