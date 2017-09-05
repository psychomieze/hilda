<?php
declare(strict_types=1);
namespace TYPO3\Hilda\Service;

class Transformation
{

    private $releaseNotesData = [
        'full_version'     => '',
        'release_type'     => '',
        'release_date'     => '',
        'additional_notes' => '',
        'md5_sums'         => '',
        'upgrade_notes'    => '',
        'changelog'        => '',
        'major_version'    => '',
        'year'             => ''
    ];

    public function transformFormDataToReleaseNotesData(array $formData): array
    {
        $this->releaseNotesData = array_merge($this->releaseNotesData, $formData);
        $this->releaseNotesData['changelog'] = nl2br($formData['changelog'], false);
        $this->extractMajorVersion($formData['full_version']);
        $this->extractDateData($formData['date']);
        $this->extractReleaseType($formData['release_type']);
        return $this->releaseNotesData;
    }

    /**
     * @param \DateTime $date
     *
     * @return mixed
     *
     */
    private function extractDateData(\DateTime $date)
    {
        /** @var \DateTime $date */
        $this->releaseNotesData['year'] = $date->format('Y');
        $this->releaseNotesData['release_date'] = $date->format('\o\n l jS F Y');
    }

    /**
     * @param string $fullVersion
     *
     * @return bool|string
     * @internal param array $formData
     *
     */
    private function extractMajorVersion(string $fullVersion)
    {
        if(strpos($fullVersion, '.') !== false) {
            $this->releaseNotesData['major_version'] = substr($fullVersion, 0, strpos($fullVersion, '.'));
        }
    }

    /**
     */
    private function extractReleaseType(string $releaseType)
    {
        switch ($releaseType) {
            case 'major':
                $this->releaseNotesData['release_type'] = 'This was a major version upgrade - running upgrade wizards, clearing caches and following the upgrade guide in full is recommended.';
                break;
            case 'minor':
                $this->releaseNotesData['release_type'] = 'Minor release only, clearing of caches is recommended - no upgrade wizards required.';
                break;
            case 'security':
                $this->releaseNotesData['release_type'] = 'This release was a security release. For more information see Security Bulletins';
                break;
            default:
        }
    }
}