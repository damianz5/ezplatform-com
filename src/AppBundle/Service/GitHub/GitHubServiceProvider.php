<?php

/**
 * GitHubServiceProvider
 *
 * Provides method to call GitHub.com API.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace AppBundle\Service\GitHub;

use AppBundle\Service\PackageRepository\PackageRepositoryServiceInterface;
use AppBundle\ValueObject\RepositoryMetadata;
use Github\Api\Repo;
use Github\Client;

/**
 * Class GitHubServiceProvider
 * @package AppBundle\Service\GitHub
 */
class GitHubServiceProvider implements PackageRepositoryServiceInterface
{
    const GITHUB_DEFAULT_BRANCH = 'HEAD';
    const GITHUB_HREF_PART = 'blob';
    const GITHUB_IMG_PART = 'raw';

    const GITHUB_URL_PARTS = [
        'href' => self::GITHUB_HREF_PART . '/' . self::GITHUB_DEFAULT_BRANCH,
        'src' => self::GITHUB_IMG_PART . '/' . self::GITHUB_DEFAULT_BRANCH,
    ];

    /**
     * @var \GitHub\Client
     */
    private $gitHubClient;

    /**
     * @var string
     */
    private $authenticationToken;

    public function __construct(Client $gitHubClient, string $authenticationToken)
    {
        $this->gitHubClient = $gitHubClient;
        $this->authenticationToken = $authenticationToken;
    }

    /**
     * @param \AppBundle\ValueObject\RepositoryMetadata $repositoryMetadata
     * @param string $format
     *
     * @return null|string
     */
    public function getReadme(RepositoryMetadata $repositoryMetadata, string $format = 'html'): ?string
    {
        try {
            return $this->getPackageRepository()->readme($repositoryMetadata->getUsername(), $repositoryMetadata->getRepositoryName(), $format);
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @return \Github\Api\Repo
     */
    private function getPackageRepository(): Repo
    {
        $this->gitHubClient->authenticate($this->authenticationToken, null, Client::AUTH_URL_TOKEN);

        return $this->gitHubClient->repository();
    }
}
