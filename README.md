<div align="center">

![Sharp 4](./docs/img/logo2.png)

</div>

Sharp is not a CMS: it's a content management framework, a toolset which provides help building a CMS section in a website, with some rules in mind:
- the public website **should not have any knowledge of the CMS** — the CMS is a part of the system, not the center of it. In fact, removing the CMS should not have any effect on the project.
- The CMS **should not have any expectations from the persistence layer**: MySQL is cool — but it's not the perfect tool for every problem. And more important, the DB structure has nothing to do with the CMS.
- Content administrators **should work with their data and terminology**, not CMS terms. I mean, if the project is about spaceships, space travels and pilots, why would the CMS talk about articles, categories and tags?
- website developers **should not have to work on the front-end development** for the CMS. Yeah. Because life is complicated enough, Sharp takes care of all the responsive / CSS / JS stuff.

Sharp intends to provide a clean solution to the following needs:
- create, update or delete any structured data of the project, handling validation and errors;
- display, search, sort or filter data;
- execute custom commands on one instance, a selection or all instances;
- handle authorizations and validation;
- all without write a line of front code, and using a clean API in the PHP app.

Sharp 4 needs Laravel 5.5+ and PHP 7.1.3+.

## Documentation

The full documentation is available here: [sharp.code16.fr/docs](http://sharp.code16.fr/docs).

## Online example

A Sharp instance for a dummy demo project is online here: [sharp.code16.fr/sharp/](http://sharp.code16.fr/sharp/). Use these accounts to login:
- admin@example.com / secret
- boss@example.com / secret (has a few more permissions)

Data of this demo is reset each hour. 

## Additional resources

Here's a series of blog posts which present Sharp following a simple example:
- [Part 1](https://medium.com/code16/about-sharp-for-laravel-part-1-74a826279fe0)
- [Part 2](https://medium.com/code16/about-sharp-for-laravel-part-2-9c7779782f31)
- [Part 3: filters](https://medium.com/code16/about-sharp-for-laravel-part-3-2bb992d6a8e3)
- [Part 4: form lists](https://medium.com/code16/about-sharp-for-laravel-part-4-cb2232caf234)
- [Part 5: commands](https://medium.com/code16/about-sharp-for-laravel-part-5-44699e270647)
- [Part 6: uploads](https://medium.com/code16/about-sharp-for-laravel-part-6-a03ee71cb2c5)

And a few more articles on specific features:
- [Dashboards](https://medium.com/code16/sharp-for-laravel-version-4-1-dashboard-generalization-69648df9baf9)
- [Multi-Forms](https://medium.com/code16/sharp-for-laravel-a-quick-presentation-of-multi-forms-fc49f0e51176)
- [What's new in 4.1](https://medium.com/code16/sharp-for-laravel-4-1-is-now-released-964c8b6b0491)
- [New features in 4.1.3](https://medium.com/code16/sharp-4-1-3-and-its-new-features-a498c8b67629)
- [New feature in 4.1.12: linked dropdowns](https://medium.com/code16/sharp-4-1-12-is-out-and-it-brings-linked-dropdowns-88d474946489)
- [New feature in 4.1.17: date range filter](https://medium.com/code16/sharp-for-laravel-4-1-17-comes-with-date-range-filters-at-last-cc93b98daf40)

