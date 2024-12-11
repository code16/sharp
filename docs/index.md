---
layout: home
title: Sharp - The Laravel Content Management Framework
hero:
  name: Sharp
  text: The Laravel Content Management Framework.
  image:
    src: /logo-home.svg
  actions:
    - theme: brand
      text: Get Started
      link: /guide
    - theme: alt
      text: Try the demo
      link: https://sharp.code16.fr/sharp
---

<div class="content">

Sharp is a content management framework built for Laravel as a package, which provides great help to build a CMS section in a project with a clean UI and DX in mind. Sharp is driven by code: everything is manageable through a clean and documented PHP API, using Laravel conventions and coding style. It intends to preserve code adherence — the project should not have any knowledge of it — and is data-agnostic, meaning it does not have any expectations from the persistence layer.

Sharp for Laravel is actively maintained and developed, and is used in all kinds of projects, from content-driven websites to e-commerce platforms and API backends.

# Main features

## Streamlined Lists

- **Customizable columns**: choose and format the data to display, and allow sorting.
- **Search and filters**: define filters for your lists, with various types and options.
- **State management**: if your entities have some state, you can manage it easily form here.

![Sharp Lists](/docs/img/readme/v9/list.png)

## Powerful Command system

- **Individual or bulk**: create commands to act on a single instance or on a selection (filtered list or user choice).
- **Forms**: easily attach forms to commands, with validation and confirmation.
- **Wizards**: create multi-steps commands with dynamic paths.

![Sharp Commands](/docs/img/readme/v9/command.png)

## Comprehensive Forms

- **Fields and layout**: use one of the many fields available, and organize them in a layout that fits your needs.
- **Powerful editor with embeds**: Sharp's custom editor really allows to create rich content, and includes a clever system to develop custom embeds.
- **Uploads**: manage files with bulk uploads, image transformation, disk and path configuration and precise validation.
- **Lists**: create lists (repeaters) of custom items in your form.

![Sharp Forms](/docs/img/readme/v9/form.png)

## Detailed Show Pages

- **Present an instance**: create a page to present an instance with a custom layout, with access to commands and state management.
- **Embedded lists**: include lists in your show page to present linked data.
- **Breadcrumb**: help your users to find their way up, allowing hierarchical navigation though embedded lists.

![Sharp Commands](/docs/img/readme/v9/show.png)

## Insightful Dashboards

- **Widgets**: use various widgets to present synthetic data, graphs and direct links.
- **Filters and commands**: leverage filters and commands in your dashboards.

![Sharp Commands](/docs/img/readme/v9/dashboard.png)

## Authentication and authorizations

- **Built-in authentication system** with fine-grained permissions managed by custom policies.
- **Remind me, rate limiting, forgotten password, impersonation** included.
- **2FA**: out-of-the-box 2FA with TOTP or notification.

## And more

- **Global search**: propose a custom global search to users.
- **Global filters**: ideal for multi-tenant applications.
- **Built-in localization**: manage translations for your entities.
- **Quick creation UI**: allow your user to efficiently create new instances.
- **Toast notifications and page alerts** to inform your users.
- **Dark mode and theme color**: the UI will adapt itself based on your primary color.
- **Artisan commands** with prompts to generate lists, forms, commands, etc.
- **Code-driven configuration** with a clean and documented API.

# About Sharp

Sharp is a long term project developed by [Code 16](https://code16.fr), a web agency based in France, since 2017. We use it in almost all our projects, and we are committed to maintaining and improving it over time.

Sharp 9 relies on Laravel 11, Tailwind, Inertia, Vue and Alpine.JS. So far we have not implemented any kind of sponsorship system, but we are open to discussing it in the future.

</div>
