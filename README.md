Music Sync
===

Intro
---

This is a console system designed to simplify non-trivial music syncing between various devices. For example,
you might have a primary music collection on your main personal machine in FLAC, but you want selected albums
to be synced to your phone in a lower bitrate. You want another set of albums sent to your work laptop, but
in FLAC.

This project is an **unfinished** piece of software. Feel free to read the code, ask questions in the Issues,
or ping me an email via my blog. I may finish it, I may not - watch this space!

Features
---

* Generate Listing - do I generate listings caches separately? Offer --for-source and --for-dest options

* Diff - just runs a diff for the specified env

* Sync - copies files based on prevailing options for specified env. Offer --dry-run too.

* Clean - deletes unwanted files e.g. hidden files. Offer --for-source and --for-dest options. Offer --dry-run too.

* Improve - identifies source improvements required, e.g. missing cover image, bit-rate too low, wrong format, etc

Todo
---

* Add a test that populates size data twice (see testRecursiveTotalSize)
* Write a directory cache
* Add progress device to recursive Directory populator (DFS)
* Can we add a BFS progress device too?
* Add a Symfony bar device to the Generate Listing command
* Add a factory to Directory

Done
---

* Recursive directory scanner
* Recursive object counter
* Recursive file total scanner
* Add sorter device to Directory class
