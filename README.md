Records Library 1.0
========================

This is an impementation of a records library system that relies on Symfony 3.0.
It allows inserting records data by simply handling the UPC code and submitting to Amazon:
if that item is present in the Amazon catalog the data will be fecthed and stored.
When that particular item (that particular UPC) is not available on Amazon, then
a simple search is possible in order to find a possible equivalent (i.e. an item
with the same media type, the same number of media, the same tracks and the same cover
image). As a last resort a manual insertion of data is possible.

What's inside?
--------------

Mainly all files not excluded by the .gitignore available here:
https://github.com/github/gitignore/blob/master/Symfony.gitignore
