# YouTube Music Library Exporter

This tiny project allows you to download in a more reliable way your YouTube Music library. I tried [many times](https://support.google.com/youtubemusic/thread/153508023?hl=en) to export all my uploads using Google Takeout but songs were missing and the export is unbelieviably messy.

With the help of [yt-dlp](https://github.com/yt-dlp/yt-dlp), I put together several scripts that will semi-automate the export process.

## Features

- Parse and format your library's song list metadata
- Download all songs (embeds song title, artist, album, and thumbnail)
- Resume-able download script
- Songs pre-clustering (by album)

Tested on macOS/PHP 8.1.

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
        "href": "https:\/\/music.youtube.com\/watch?v=iS2LWA7-hkM",
        "is_liked": null,
        "title": "Paris"
    },
    {
        "id": "UiEfsYkr4gY",
        "album": null,
        "artist": "The Chainsmokers",
        "duration": "3:52",
        "href": "https:\/\/music.youtube.com\/watch?v=UiEfsYkr4gY",
        "is_liked": true,
        "title": "Paris (BKAYE Remix)"
    }
]
```

To generate it, follow these steps:

1. Open <https://music.youtube.com/library/uploaded_songs>
1. Scroll all the way to the bottom of your list (this may take a while if you have thousands of songs)
1. Save the page (`CTRL`/`CMD`+`S`) as HTML-only into this project's clone directory
1. Run the `metadata-extractor` script:

```bash
❯ ./metadata-extractor

YouTube Music Upload Metadata Extractor
=======================================

 Will extract ./metadata.json from ./YouTube Music.html:
 7369/7369 [============================] 100% 7 secs

 [OK] done
```

You have now a `metadata.json` file containing all the songs info of your entire library. This file is the source of truth for the next scripts. You may add manually missing data if needed.

See the script's help: `./metadata-extractor --help`.

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
[2022-03-14T11:34:22.799174+00:00] downloader.INFO: success {"id":"NZjN83C1K9M","song_path":"./downloads/NZjN83C1K9M.m4a"} []
[2022-03-14T11:34:30.753464+00:00] downloader.INFO: success {"id":"xIU9tu9l_co","song_path":"./downloads/xIU9tu9l_co.m4a"} []
[2022-03-14T11:34:37.341654+00:00] downloader.INFO: success {"id":"ca3w36Ua_aM","song_path":"./downloads/ca3w36Ua_aM.m4a"} []
```

By default, the script downloads all into the `./downloads` directory and flattens all songs named by their ID. Each song has its metadata/thumbnail embedded.

See the script's help: `./downloader --help`.

## Songs clustering

Use the `clusterer` script to cluster your songs. It will use the `metadata.json` file and songs downloaded into the `./downloads` directory.

By default, the script clusters all into the `./clusters` directory and creates one sub-directory per album and leaves the songs without album in the first level:

```
clusters
├── Guiding Light - Gettin' Rolled Out
│   ├── Gettin' Rolled Out.m4a
│   └── Guiding Light (feat. Ali).m4a
├── Guru Josh Project - Infinity 2008 (Klaas Remix).m4a
├── Guys My Age (Prince Fox Remix)
│   └── Guys My Age (Prince Fox Remix).m4a
├── Ham Demo and Justin Time - Here I Am (Ham 2005 Remix).m4a
├── Happy morning
│   ├── Get Down On It.m4a
│   ├── High Energy.m4a
│   ├── I got you (I feel good).m4a
│   ├── Ring My Bell.m4a
│   └── We are family (live).m4a
├── Hardcore 2004 - You Ready?
│   ├── Hardcore 2004.m4a
│   └── You Ready?.m4a
├── Hardcore Classics Disc 3
│   └── Be Happy.m4a
├── Harlea - Miss Me.m4a
├── Haze and Weaver vs. Shox - Get up 2004.m4a
├── Heartburn
│   └── Heartburn.m4a
```

See the script's help: `./clusterer --help`.

## Playlists

For playlists, it's basically the same process as the metadata extractor: save the playlist HTML page locally, then run the script `playlist-extrator`. It will create a basic text file of this format:

```
[title] by [artist]
[title] by [artist]
...
```

See the script's help: `./playlist-extractor --help`.

Adapt the format to your needs.

:warning: Some titles in the playlist aren't part of your library so you may need to download them first.

## Metadata polishing

If you like your library to be clean and accurate, you can leverage [MusicBrainz Picard](https://picard.musicbrainz.org/)'s Scan feature to fix and rename your songs.

Once again, this is a long and manual process but hopefully, you do only have to do it once.

---

## Caveats

- Song order is not restored and metadata information is at its most basic. This is still way better than the regular Takeout export. The most automated way to restore the song order would be to crawl each album/playlist page and parse it using the metadata extractor script, then update the clusterer script logic to handle order file naming.
- Some steps are still manual and tedious and could/should be automated. I believe the songs page HTML source fetching could be done using [Taiko](https://taiko.dev/).
- Song downloading process is rather slow (~10s per song) BUT it could be faster if you decide to skip the song's metadata and/or thumbnail. I was able to download all my 7k+ songs library in a couple of hours vs a day when embedding all these info.
