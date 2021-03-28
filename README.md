Features
---

* Generate Listing - do I generate listings caches separately? Offer --for-source and --for-dest options

* Diff - just runs a diff for the specified env

* Sync - copies files based on prevailing options for specified env. Offer --dry-run too.

* Clean - deletes unwanted files e.g. hidden files. Offer --for-source and --for-dest options. Offer --dry-run too.

* Improve - identifies source improvements required, e.g. missing cover image, bit-rate too low, wrong format, etc

Todo
---

* Add progress device to recursive Directory populator (DFS)
* Can we add a BFS progress device too?
* Add a Symfony bar device to the Generate Listing command
* Add a factory to Directory

Done
---

* Recursive directory scanner
* Add sorter device to Directory class
