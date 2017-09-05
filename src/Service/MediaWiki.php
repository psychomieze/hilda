<?php
declare(strict_types=1);
namespace TYPO3\Hilda\Service;

use Mediawiki\Api\ApiUser;
use Mediawiki\Api\MediawikiApi;
use Mediawiki\Api\MediawikiFactory;
use Mediawiki\DataModel\Content;
use Mediawiki\DataModel\PageIdentifier;
use Mediawiki\DataModel\Revision;
use Mediawiki\DataModel\Title;

class MediaWiki
{
    /**
     * @param $template
     * @param $releaseNotesData
     */
    public function saveReleaseNotesToWiki($template, $releaseNotesData)
    {
        $api = new MediawikiApi('https://wiki.typo3.org/wiki/api.php');
        $api->login(new ApiUser(getenv('WIKI_USER'), getenv('WIKI_PASS')));
        $services = new MediawikiFactory($api);

        $newContent = new Content($template);
        $title = new Title('TYPO3 CMS ' . $releaseNotesData['full_version']);
        $identifier = new PageIdentifier($title);
        $revision = new Revision($newContent, $identifier);
        $services->newRevisionSaver()->save($revision);
    }
}