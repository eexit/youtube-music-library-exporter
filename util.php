<?php

declare(strict_types=1);

function getDownloadedSong(string $downloadsDir, string $id): string
{
    $path = $downloadsDir.DIRECTORY_SEPARATOR.$id;
    
    if (false === $files = glob($path.'.*')) {
        throw new \RuntimeException('glob error for path:'.$path);
    }

    return match (\count($files)) {
        0 => throw new \InvalidArgumentException('song not found for ID: '.$id),
        1 => $files[0],
        default => throw new \UnexpectedValueException('found duplicates or uncomplete parts for song ID: '.json_encode($files))
    };
}
