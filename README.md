# Sharp 4

> Sharp is under heavy development. It should be out and documented in a few weeks.

Sharp is not a CMS: it's a content management package, a toolset which provides help
building a CMS section in a website, with some rules in mind:
- the public website should not have any knowledge of the CMS;
- the CMS should not have any expectations from the persistence layer (meaning: the DB structure has nothing to do with the CMS);
- in fact, removing the CMS should not have any effect on the project (well, ok, except for the administrator);
- administrators should work with their data and terminology, not CMS terms;
- website developers should not have to work on the front-end development for the CMS, but should have a 
 
Sharp intends to provide a clean solution for the following needs:
- create, update or delete any structured data of the project, handling validation and errors;
- display, search, sort or filter data;
- execute custom commands on one instance or a selection of instances.

Sharp 4 needs Laravel 5.4+ and PHP 7.0+.