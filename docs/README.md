---
home: true
heroImage: /img/sharplogo.png
heroText: null
actionText: Get Started →
actionLink: /guide/
tagline: The Content Management Framework for Laravel
footer: Code 16 — 2021
pageClass: home-page
---

Sharp is a content management framework built for Laravel, a toolset which provides help to build a CMS section in a website, with some rules in mind:
- the public website **should not have any knowledge of the CMS** — the CMS is a part of the system, not the center of it. In fact, removing the CMS should not have any effect on the project.
- Content administrators **should work with their data and terminology**, not CMS terms. I mean, if the project is about spaceships, space travels and pilots, why would the CMS talk about articles, categories and tags?
- Developers **should not have to work on the front-end development for the CMS**. Because life is complicated enough, Sharp takes care of all the responsive / CSS / JS stuff.
- The CMS **should not have any expectations from the persistence layer**: MySQL is cool — but it's not the perfect tool for every problem. And more important, the DB structure has nothing to do with the CMS.

### Main features 

#### Build complex lists with total control on how the data is presented

![Entity list](./img/readme/list.jpg)

#### Organize forms withs various fields, a customizable layout system and data validation
You will find a powerful HTML / Markdown editor, autocompletes with templates, lists (repeaters) with custom items, files with bulk upload and image transformation...

![Entity list](./img/readme/form.jpg)

#### Optionally create show pages for your resources, with embedded lists for linked data, and breadcrumb

![Show page](./img/readme/show.jpg)

#### Add filters, sorting columns and search to your lists

![Filters](./img/readme/filters.jpg)

#### Add individual or bulk commands, with dedicated forms, user confirmation...

A Command is an action presented to the user (with permissions handled via policies, like everywhere in Sharp) which can lead to refreshing data after update, previewing a public page, downloading a file...

![Commands](./img/readme/command-form.jpg)

#### Present synthetic data, graphs and direct links in dashboards

TODO

#### Choose your theme color!

Choose one color, and the UI will adapt itself.

![Colors](./img/readme/colors.jpg)

#### Driven by code, with DX in mind

Everything in Sharp is manageable through a clean and documented PHP API, using Laravel conventions and coding style.  


### Online demo

A Sharp instance for a demo project is online here: [sharp.code16.fr/sharp/](http://sharp.code16.fr/sharp/). 

Data of this demo is reset each hour. 
