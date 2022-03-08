<div align="center">

![Sharp](./docs/img/sharplogo.png)

</div>

Sharp is a content management framework, a toolset which provides help to build a CMS section in a website, with some rules in mind:
- the public website **should not have any knowledge of the CMS** — the CMS is a part of the system, not the center of it. In fact, removing the CMS should not have any effect on the project.
- Content administrators **should work with their data and terminology**, not CMS terms. I mean, if the project is about spaceships, space travels and pilots, why would the CMS talk about articles, categories and tags?
- Developers **should not have to work on the front-end development for the CMS**. Because life is complicated enough, Sharp takes care of all the responsive / CSS / JS stuff.
- The CMS **should not have any expectations from the persistence layer**: MySQL is cool — but it's not the perfect tool for every problem. And more important, the DB structure has nothing to do with the CMS.

Sharp intends to provide a clean solution to the following needs:
- create, update or delete any structured data of the project, handling validation and errors;
- display, search, sort or filter data;
- execute custom commands on one instance, a selection or all instances;
- handle authorizations and validation;
- all without write a line of front code, and using a clean API in the PHP app.

Sharp needs Laravel 8+ and PHP 8.0+.

## Documentation

The full documentation is available here: [sharp.code16.fr/docs](http://sharp.code16.fr/docs).

## Online example

A Sharp instance for a demo project is online here: [sharp.code16.fr/sharp/](http://sharp.code16.fr/sharp/).

Data of this demo is reset each hour. 

## Additional resources

See the Sharp section of the Code 16 Medium account:
[https://medium.com/code16/tagged/sharp](https://medium.com/code16/tagged/sharp)
