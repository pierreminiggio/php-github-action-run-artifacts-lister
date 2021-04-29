<?php

namespace PierreMiniggio\GithubActionRunArtifactsLister;

use PierreMiniggio\GithubActionRunArtifactsLister\Exception\NotFoundException;
use PierreMiniggio\GithubActionRunArtifactsLister\Exception\UnknownException;
use PierreMiniggio\GithubUserAgent\GithubUserAgent;
use RuntimeException;

class GithubActionRunArtifactsLister
{

    /**
     * @return GithubActionRunArtifact[]
     * 
     * @throws NotFoundException
     * @throws RuntimeException
     */
    public function list(
        string $owner,
        string $repo,
        int $runId
    ): array
    {

        $curl = curl_init("https://api.github.com/repos/$owner/$repo/actions/runs/$runId/artifacts");
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT => GithubUserAgent::USER_AGENT
        ]);

        $response = curl_exec($curl);

        if ($response === false) {
            throw new RuntimeException('Curl error' . curl_error($curl));
        }

        $jsonResponse = json_decode($response, true);

        if ($jsonResponse === null) {
            throw new RuntimeException('Bad Github API return : Bad JSON');
        }

        if (! empty($jsonResponse['message'])) {
            $message = $jsonResponse['message'];

            if ($message === 'Not Found') {
                throw new NotFoundException();
            }

            throw new UnknownException($message);
        }

        if (! isset($jsonResponse['total_count'])) {
            throw new RuntimeException('Bad Github API return : "total_count" missing');
        }

        if ((int) $jsonResponse['total_count'] === 0) {
            return [];
        }

        if (! isset($jsonResponse['artifacts'])) {
            throw new RuntimeException('Bad Github API return : "artifacts" missing');
        }

        return array_map(fn (array $fetchedArtifact): GithubActionRunArtifact => new GithubActionRunArtifact(
            (int) $fetchedArtifact['id'],
            $fetchedArtifact['name'],
            (bool) $fetchedArtifact['expired']
        ), $jsonResponse['artifacts']);
    }
}
