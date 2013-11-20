Torrent Bot
=======
What does the bot do?
Read a list of key topics
Check torrent tracker[s] for new ebooks
Inform a master

Install
=======

Usage
=====

Data structures
===============

Design
======

Key Collector - keys.php
Torrent checker
    html to string
    string to array of blocks
    blocks to array of elements
    elements to json gzip file
Filter
    new db to array of blocks
    if global db exists
       global db to array of blocks
       filter uniq blocks
       add uniq blocks to global
    if global db does not exist
       add new blocks to global

Reporter
