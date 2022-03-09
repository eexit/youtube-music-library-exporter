# Youtube Music Library Exporter

This tiny project allows you to download in a reliable way your YouTube Music library. I tried [many times](https://support.google.com/youtubemusic/thread/153508023?hl=en) to export all my uploads using Google Takeout but songs were missing and the export is unbelieviably messy.

With the help of [yt-dlp](https://github.com/yt-dlp/yt-dlp), I put together several scripts that will semi-automate the export process.

## Features

- Parse and format your library's song list metadata
- Download all songs (embeds song title, artist, album, and thumbnail)
- Resume-able download script
- Flexible songs clustering ("artist - album" by default)

## Installation

Clone this repository anywhere:

```bash
cd ~/Music
git clone git@github.com:eexit/youtube-music-library-exporter.git
cd youtube-music-library-exporter
```

Install dependencies (assuming you have [homebrew](https://brew.sh/) installed):

```bash
brew install yt-dlp/taps/yt-dlp ffmpeg php composer
composer install
```

## Build the metadata file

The metadata file is a JSON file containing all your uploaded songs basic information.

Sample:

```json
[
    {
        "id": "iS2LWA7-hkM",
        "album": "Emotional Technology",
        "artist": "BT",
        "duration": "7:48",
        "href": "https:\/\/music.youtube.com\/watch?v=iS2LWA7-hkM&list=MLPT",
        "is_liked": null,
        "title": "Paris"
    },
    {
        "id": "UiEfsYkr4gY",
        "album": null,
        "artist": "The Chainsmokers",
        "duration": "3:52",
        "href": "https:\/\/music.youtube.com\/watch?v=UiEfsYkr4gY&list=MLPT",
        "is_liked": true,
        "title": "Paris (BKAYE Remix)"
    }
]
```

1. Open <https://music.youtube.com/library/uploaded_songs>
1. Scroll all the way to the bottom of your list (this may take a while if you have thousands of songs)
1. Save the page (`CTRL`/`CMD`+`S`) as HTML-only into this project's clone directory
1. Run the `metadata-builder` script:

```bash
❯ ./metadata-builder

YouTube Music Upload Metadata Generator
=======================================

 Will build ./metadata.json from ./YouTube Music.html:
 7369/7369 [============================] 100% 7 secs

 [OK] done
```

You have now a `metadata.json` file containing all the songs info of your entire library. This file is the source of truth for the next scripts. You may add manually missing data like artist or album if required by the de-cluttering script.

See the script's help: `./metadata-builder --help`.

## Download the songs

Before you start, you need to grab your YouTube Music's session cookies dumped by the "Get cookies.txt" extension. Save the file into this project's clone directory.

Use the `downloader` script to download all the songs listed in the `metadata.json` file:

```bash
❯ ./downloader

YouTube Music Upload Downloader
===============================

 Will now download 7368 songs:
 3697/7368 [==============>-------------]  50% 10 hrs
```

You can watch for the logs:

```bash
❯ tail -f downloader.log
[2022-03-09T17:39:32.453347+00:00] downloader.INFO: success {"id":"k8alCaY3Abs","song_path":"./downloads/k8alCaY3Abs"} []
[2022-03-09T17:39:58.769715+00:00] downloader.INFO: success {"id":"yBbwMt60j2k","song_path":"./downloads/yBbwMt60j2k"} []
[2022-03-09T17:40:14.103107+00:00] downloader.INFO: success {"id":"b9-iV9fRkR8","song_path":"./downloads/b9-iV9fRkR8"} []
```

By default, the script downloads all into the `./downloads` directory and flattens all songs named by their ID. Each song has its metadata/thumbnail embedded.

See the script's help: `./downloader --help`.

## Songs clustering

Use the `clusterer` script to cluster your songs. It will use the `metadata.json` file and songs downloaded into the `./downloads` directory:

```bash
```

---

## Caveats

Some steps are still manual and tedious and could/should be automated. I believe the songs page HTML source fetching could be done using [Taiko](https://taiko.dev/).

Song downloading process is rather slow (~10s per song) BUT it could be faster if you decide to skip the song's metadata and/or thumbnail. I was able to download all my 7k+ songs library in a few hours vs a day when omitting all these info.
I believe we could improve this part by adding some concurrency :-)


