<?php

namespace PierreMiniggio\GithubActionRunArtifactsLister;

class GithubActionRunArtifact
{
    public function __construct(
        public int $id,
        public string $name,
        public bool $expired
    )
    {
    }
}
