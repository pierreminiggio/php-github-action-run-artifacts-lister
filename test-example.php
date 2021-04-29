<?php

use PierreMiniggio\GithubActionRunArtifactsLister\GithubActionRunArtifactsLister;

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$lister = new GithubActionRunArtifactsLister();
$list = $lister->list(
    'pierreminiggio',
    'remotion-test-github-action',
    789704536
);

var_dump($list);
