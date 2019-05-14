---
home: true
heroImage: /img/logo2.png
heroText: null
actionText: Get Started →
actionLink: /guide/
tagline: The Content Management Framework for Laravel
footer: Code 16 — 2019
pageClass: home-page
---

Sharp is not a CMS: it's a content management framework, a toolset which provides help building a CMS section in a website, with some rules in mind:
- the public website **should not have any knowledge of the CMS** — the CMS is a part of the system, not the center of it. In fact, removing the CMS should not have any effect on the project.
- The CMS **should not have any expectations from the persistence layer**: MySQL is cool — but it's not the perfect tool for every problem. And more important, the DB structure has nothing to do with the CMS.
- Content administrators **should work with their data and terminology**, not CMS terms. I mean, if the project is about spaceships, space travels and pilots, why would the CMS talk about articles, categories and tags?
- website developers **should not have to work on the front-end development** for the CMS. Yeah. Because life is complicated enough, Sharp takes care of all the responsive / CSS / JS stuff.

<div class="my-5">
    <div class="section my-3">
        <div class="row mx-n2">
            <div class="col px-2">
                <div class="card">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100" class="heroicon-form heroicon heroicons-lg">
                        <path class="heroicon-form-clipboard heroicon-component-accent heroicon-component-fill" d="M0 16.01A6 6 0 0 1 6 10h66a6 6 0 0 1 6 6.01v77.98a6 6 0 0 1-6 6.01H6a6 6 0 0 1-6-6.01V16.01z"></path>
                        <polygon class="heroicon-form-pages heroicon-component-fill" points="7 14 7 90 71 90 71 14"></polygon>
                        <path class="heroicon-form-clip heroicon-component-accent heroicon-component-fill" d="M24 8.9a39.7 39.7 0 0 1 7.1-2.12 8 8 0 0 1 15.8 0c2.46.5 4.83 1.2 7.1 2.13V19H24V8.9zM39 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"></path>
                        <polygon class="heroicon-form-pen-housing heroicon-component-fill" points="90 22 99 22 99 25 96 25 96 79 93 90 90 79"></polygon>
                        <path class="heroicon-form-pen-button heroicon-component-accent heroicon-component-fill" d="M90 15h5v7s4-.24 4 0v3h-9V15z"></path>
                        <rect class="heroicon-form-pen-grip heroicon-component-accent heroicon-component-fill" width="6" height="24" x="90" y="55"></rect>
                        <path class="heroicon-shadows" d="M23 20h32v2H23v-2zM9 85h60v3H9v-3zM6 98a3.99 3.99 0 0 1-3.55-2.16c1 .73 2.22 1.16 3.54 1.16h66.02c1.32 0 2.55-.43 3.54-1.16A4 4 0 0 1 72.01 98H5.99z"></path>
                        <path class="heroicon-outline" fill-rule="nonzero" d="M55 9.33V10h17.16A6 6 0 0 1 78 16V93.99a6 6 0 0 1-6 6.01H6a6 6 0 0 1-6-6.01V16a6 6 0 0 1 6-6h17v-.67a39.7 39.7 0 0 1 8.1-2.55 8 8 0 0 1 15.8 0A39.7 39.7 0 0 1 55 9.33zM55 14h16v76H7V14h16v-2H6a4 4 0 0 0-4 4.01V94c.91 1.22 2.36 2 4 2h66c1.64 0 3.09-.78 4-2V16.01A4 4 0 0 0 72 12H55v2zM9 16v67h60V16H55v4H23v-4H9zm10.5 47a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm1.5-2.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0zm5-.5h24v1H26v-1zm35 7v1H17v-1h44zm-20 6v1H17v-1h24zm-24-3h40v1H17v-1zm40-41v1H17v-1h40zm-40 3h42v1H17v-1zm24 3v1H17v-1h24zM25 17v1h28v-1H25zm0-1h28v-5.34a37.7 37.7 0 0 0-6.49-1.92l-1.37-.27-.2-1.39a6 6 0 0 0-11.87 0l-.21 1.39-1.37.27A37.7 37.7 0 0 0 25 10.66V16zm14-5a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm2-3a2 2 0 1 0-4 0 2 2 0 0 0 4 0zM6 98h66a4 4 0 0 0 3.55-2.16A5.96 5.96 0 0 1 72.01 97H5.99a5.96 5.96 0 0 1-3.54-1.16c.67 1.28 2 2.16 3.54 2.16zm63-13H9v1h35v1H9v1h60v-1h-6v-1h6v-1zm21-69v-2h6v7h2a2 2 0 0 1 2 2v18a1 1 0 1 1-2 0V26h-1v53l-3 12h-2l-3-12V21h1v-5zm4 0h-2v5h2v-5zm-3 8h7v-1h-7v1zm4 1h-4v30h4V25zm-4 32h4v-1h-4v1zm4 20h-4v1h4v-1zm-2 9.75L94.94 79h-3.88L93 86.75zM95 76V58h-4v18h4zM26 54h24v1H26v-1zm-6.5 3a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm1.5-2.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0zm5-6.5h24v1H26v-1zm-6.5 3a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm1.5-2.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0zm5-6.5h24v1H26v-1zm-4 .5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zM19.5 44a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM17 26h44v1H17v-1z"></path>
                    </svg>
                    <h2 class="section-title">
                        Create, update or delete any structured data of the project, handling validation and errors.
                    </h2>
                </div>
            </div>
            <div class="col px-2">
                <div class="card">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100" class="heroicon-magnify heroicon heroicons-lg">
                        <path class="heroicon-magnify-glass-edge heroicon-component-accent heroicon-component-fill" d="M70 36a34 34 0 1 1-68 0 34 34 0 0 1 68 0z"></path>
                        <path class="heroicon-magnify-glass heroicon-component-fill" d="M61 36a25 25 0 1 1-50 0 25 25 0 0 1 50 0z"></path>
                        <polygon class="heroicon-magnify-handle heroicon-component-fill" points="94.879 87.707 75.293 68.121 68.121 75.293 87.707 94.879"></polygon>
                        <path class="heroicon-magnify-handle-connector heroicon-component-fill" d="M63.92 58.73L65.16 60 60 65.17l-1.26-1.26a36.22 36.22 0 0 0 5.18-5.18zm-.5 5.86l1.17-1.18 4 4-1.18 1.18-4-4z"></path>
                        <path class="heroicon-magnify-handle-edge heroicon-component-accent heroicon-component-fill" d="M90 97.17l-1.59-1.58 7.18-7.18L97.17 90 90 97.17zM73 65.83l1.59 1.58-7.18 7.18L65.83 73 73 65.83z"></path>
                        <path class="heroicon-shadows" d="M6.04 37.5a30 30 0 1 1 59.93 0 30 30 0 0 0-59.93 0z"></path>
                        <path class="heroicon-outline" fill-rule="nonzero" d="M65.14 57.14l1.45 1.45L68 60l-1.41 1.41L66 62l4 4 1.59-1.59L73 63l1.41 1.41L76 66l.7.7 19.6 19.6.7.7 1.59 1.59L100 90l-1.41 1.41-7.18 7.18L90 100l-1.41-1.41L87 97l-.7-.7-19.6-19.6-.7-.7-1.59-1.59L63 73l1.41-1.41L66 70l-4-4-.59.59L60 68l-1.41-1.41-1.45-1.45h.01a36 36 0 1 1 8-8zM70 36a34 34 0 1 0-68 0 34 34 0 0 0 68 0zm-6.08 22.73a36.22 36.22 0 0 1-5.18 5.18L60 65.17 65.17 60l-1.26-1.26zm-.5 5.86l4 4 1.17-1.18-4-4-1.18 1.18zM90 97.17L97.17 90l-1.58-1.59-7.18 7.18L90 97.17zm4.88-9.46L75.29 68.12l-7.17 7.17 19.59 19.59 7.17-7.17zM73 65.83L65.83 73l1.58 1.59 7.18-7.18L73 65.83zM66 36a30 30 0 1 1-60 0 30 30 0 0 1 60 0zM36 65a29 29 0 1 0 0-58 29 29 0 0 0 0 58zm0-2a27 27 0 1 1 0-54 27 27 0 0 1 0 54zm25-27a25 25 0 1 0-50 0 25 25 0 0 0 50 0zM36 15v1a20 20 0 0 0-17.9 28.95l-.89.44A21 21 0 0 1 36 15zm37.65 61.35l.7-.7 14 14-.7.7-14-14z"></path>
                    </svg>
                    <h2 class="section-title">
                        Display, search, sort or filter data
                    </h2>
                </div>
            </div>
            <div class="col px-2">
                <div class="card">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100" class="heroicon-cog heroicon heroicons-lg">
                        <path class="heroicon-cog-front-outer heroicon-component-fill" d="M39.17 26.08l.13-1.6.54-6.48h4.32l.54 6.49.13 1.6 1.59.21a31 31 0 0 1 3.61.72l1.53.4.74-1.4 3.05-5.8 3.98 1.68-2.03 6.14-.5 1.53 1.4.82c1.08.64 2.08 1.31 3.09 2.07l1.27.97 1.23-1.03 4.97-4.21 3.05 3.05-4.2 4.97-1.04 1.23.97 1.27c.75 1 1.41 1.98 2.04 3.06l.8 1.36 1.52-.47 6.26-1.94 1.62 4L74 47.65l-1.44.72.4 1.57c.32 1.2.55 2.39.73 3.65l.22 1.59 1.6.13 6.48.54v4.32l-6.49.54-1.6.13-.21 1.59a31 31 0 0 1-.72 3.61l-.4 1.53 1.4.74 5.8 3.05-1.68 3.98-6.14-2.03-1.53-.5-.82 1.4a31.2 31.2 0 0 1-2.07 3.09l-.97 1.27 1.03 1.23 4.21 4.97-3.05 3.05-4.97-4.2-1.23-1.04-1.27.97a31.2 31.2 0 0 1-3.06 2.04l-1.36.8.47 1.52 1.94 6.26-4 1.62L52.35 90l-.72-1.44-1.57.4c-1.2.32-2.39.55-3.65.73l-1.59.22-.13 1.6-.54 6.48h-4.32l-.54-6.49-.13-1.6-1.59-.21a31 31 0 0 1-3.61-.72l-1.53-.4-.74 1.4-3.05 5.8-3.98-1.68 2.03-6.14.5-1.53-1.4-.82a31.2 31.2 0 0 1-3.09-2.07l-1.27-.97-1.23 1.03-4.97 4.21-3.05-3.05 4.2-4.97 1.04-1.23-.97-1.27c-.76-1-1.43-2-2.07-3.1l-.82-1.39-1.53.5-6.14 2.03-1.69-3.98 5.8-3.05 1.4-.74-.4-1.53a31 31 0 0 1-.7-3.61l-.23-1.59-1.6-.13L2 60.16v-4.32l6.49-.54 1.6-.13.21-1.59a31 31 0 0 1 .72-3.61l.4-1.53-1.4-.74-5.8-3.05 1.68-3.98 6.14 2.03 1.53.5.82-1.4a31.2 31.2 0 0 1 2.07-3.09l.97-1.27-1.03-1.23-4.21-4.97 3.05-3.05 4.97 4.2 1.23 1.04 1.27-.97c1-.75 1.98-1.41 3.06-2.05l1.36-.8-.47-1.51-1.94-6.26 4-1.62L31.65 26l.72 1.44 1.57-.4c1.2-.32 2.39-.55 3.65-.73l1.59-.22zM42 72a14 14 0 1 0 0-28 14 14 0 0 0 0 28z"></path>
                        <path class="heroicon-cog-front-inner heroicon-component-fill" d="M42 72a14 14 0 1 1 0-28 14 14 0 0 1 0 28zm0-5a9 9 0 1 0 0-18 9 9 0 0 0 0 18z"></path>
                        <path class="heroicon-cog-back-outer heroicon-component-accent heroicon-component-fill" d="M76.87 9.03l-1.53-.38c-.7-.17-1.38-.3-2.11-.41L71.66 8l-.13-1.58L71.16 2h-2.32l-.37 4.42L68.34 8l-1.57.24c-.73.1-1.4.24-2.11.41l-1.53.38-.73-1.39-2.08-3.95-2.13.9 1.37 4.2.5 1.51-1.37.82c-.63.38-1.2.77-1.8 1.2l-1.27.95-1.21-1.02-3.39-2.87-1.64 1.64 2.87 3.39 1.02 1.21-.94 1.27c-.44.6-.83 1.17-1.2 1.8l-.83 1.37-1.52-.5-2.55-.84.25 3.07 1.16.61 1.4.73-.39 1.52c.64.13 1.26.27 1.88.43l3.9-7.43 7.2 3.06a12.5 12.5 0 0 1 17.7 17.6l3 7.37-7.42 3.74c.16.64.31 1.28.44 1.93l1.55-.4.72 1.43.58 1.14 3.04.25-.8-2.6-.47-1.49 1.34-.8c.64-.39 1.2-.77 1.8-1.2l1.27-.95 1.21 1.02 3.39 2.87 1.64-1.64-2.87-3.39-1.02-1.21.94-1.27c.44-.6.83-1.17 1.2-1.8l.83-1.37 1.52.5 4.18 1.37.9-2.13-3.94-2.08-1.4-.73.39-1.52c.17-.71.3-1.39.41-2.12l.24-1.57 1.58-.13 4.42-.37v-2.32l-4.42-.37-1.58-.13-.24-1.57c-.1-.74-.24-1.41-.42-2.12l-.38-1.55 1.42-.72 3.94-1.99-.87-2.15-4.27 1.33-1.5.46-.8-1.34a21.2 21.2 0 0 0-1.2-1.8l-.95-1.27 1.02-1.21 2.87-3.39-1.64-1.64-3.39 2.87-1.21 1.02-1.27-.94c-.6-.44-1.17-.83-1.8-1.2l-1.37-.83.5-1.52L81.8 4.6l-2.13-.9-2.08 3.94-.73 1.4z"></path>
                        <path class="heroicon-cog-back-inner heroicon-component-accent heroicon-component-fill" d="M59.2 28.67l2-6.08A11.5 11.5 0 1 1 77.29 38.9l-5.97 1.85c-.57-.96-1.18-1.89-1.84-2.78l.02.01.51.02a8 8 0 1 0-7.98-7.49l.13.1c-.95-.7-1.93-1.35-2.95-1.94z"></path>
                        <path class="heroicon-shadows" d="M55.86 56a16 16 0 0 0-27.72 0 14 14 0 0 1 27.72 0zm4.16-29.82l1.19-3.6a11.5 11.5 0 0 1 20.06 5.1 13.98 13.98 0 0 0-21.25-1.5z"></path>
                        <path class="heroicon-outline" fill-rule="nonzero" d="M49.4 17.66c.41-.67.85-1.33 1.32-1.96l-4.05-4.8 4.24-4.23 4.8 4.05c.62-.47 1.28-.9 1.95-1.31l-1.95-5.93 5.52-2.35 2.94 5.58c.76-.19 1.53-.34 2.3-.45L67 0h6l.52 6.26c.78.11 1.55.26 2.3.45l2.95-5.58 5.52 2.35-1.95 5.93c.67.4 1.33.84 1.96 1.31l4.8-4.05 4.23 4.24-4.05 4.8c.46.62.9 1.27 1.3 1.95l6.03-1.87 2.25 5.56-5.58 2.81c.2.76.35 1.53.46 2.32L100 27v6l-6.26.52c-.11.78-.26 1.55-.45 2.3l5.58 2.95-2.35 5.52-5.93-1.95c-.4.67-.84 1.33-1.31 1.96l4.05 4.8-4.24 4.23-4.8-4.05c-.62.46-1.27.9-1.95 1.3l1.05 3.37.61.05v1.92l.21.7-.21.08V62l-8.32.7c-.18 1.3-.44 2.58-.76 3.83l7.43 3.9-3.13 7.37-7.89-2.6a34.05 34.05 0 0 1-2.2 3.3l5.4 6.37-5.66 5.66-6.38-5.4a34.03 34.03 0 0 1-3.24 2.18l2.48 8.01-7.41 3-3.74-7.41c-1.27.33-2.57.59-3.89.77L46 100h-8l-.7-8.32c-1.3-.18-2.58-.44-3.83-.76l-3.9 7.43-7.37-3.13 2.6-7.89a34.05 34.05 0 0 1-3.3-2.2l-6.37 5.4-5.66-5.66 5.4-6.38a34.05 34.05 0 0 1-2.2-3.29l-7.89 2.6-3.13-7.36 7.43-3.91a33.83 33.83 0 0 1-.76-3.84L0 62v-8l8.32-.7c.18-1.3.44-2.58.76-3.83l-7.43-3.9 3.13-7.37 7.89 2.6c.67-1.14 1.4-2.24 2.2-3.3l-5.4-6.37 5.66-5.66 6.38 5.4a34.03 34.03 0 0 1 3.24-2.18l-2.48-8.01 7.41-3 3.74 7.41c1.27-.33 2.57-.59 3.89-.77L38 16h5.35l.13-.29.88.29H46l.05.56 3.36 1.1zm-10.23 8.42l-1.59.22c-1.26.18-2.44.41-3.65.73l-1.57.4-.72-1.44-2.91-5.77-4 1.62 1.93 6.26.47 1.51-1.36.8a31.2 31.2 0 0 0-3.06 2.05l-1.27.97-1.23-1.03-4.97-4.21-3.05 3.05 4.2 4.97 1.04 1.23-.97 1.27c-.76 1-1.43 2-2.07 3.1l-.82 1.39-1.53-.5-6.14-2.03-1.69 3.98 5.8 3.05 1.4.74-.4 1.53a31 31 0 0 0-.7 3.61l-.23 1.59-1.6.13-6.48.54v4.32l6.49.54 1.6.13.21 1.59a31 31 0 0 0 .72 3.61l.4 1.53-1.4.74-5.8 3.05 1.68 3.98 6.14-2.03 1.53-.5.82 1.4a31.2 31.2 0 0 0 2.07 3.09l.97 1.27-1.03 1.23-4.21 4.97 3.05 3.05 4.97-4.2 1.23-1.04 1.27.97c1 .76 2 1.43 3.1 2.07l1.39.82-.5 1.53-2.03 6.14 3.98 1.69 3.05-5.8.74-1.4 1.53.4a31 31 0 0 0 3.61.7l1.59.23.13 1.6.54 6.48h4.32l.54-6.49.13-1.6 1.59-.21c1.26-.18 2.44-.41 3.65-.73l1.57-.4.72 1.44 2.91 5.77 4-1.62-1.93-6.26-.47-1.51 1.36-.8a31.2 31.2 0 0 0 3.06-2.05l1.27-.97 1.23 1.03 4.97 4.21 3.05-3.05-4.2-4.97-1.04-1.23.97-1.27c.76-1 1.43-2 2.07-3.1l.82-1.39 1.53.5 6.14 2.03 1.69-3.98-5.8-3.05-1.4-.74.4-1.53a31 31 0 0 0 .7-3.61l.23-1.59 1.6-.13 6.48-.54v-4.32l-6.49-.54-1.6-.13-.21-1.59c-.18-1.26-.41-2.44-.73-3.65l-.4-1.57 1.44-.72 5.77-2.91-1.62-4-6.26 1.93-1.51.47-.8-1.36a31.2 31.2 0 0 0-2.05-3.06l-.97-1.27 1.03-1.23 4.21-4.97-3.05-3.05-4.97 4.2-1.23 1.04-1.27-.97c-1-.76-2-1.43-3.1-2.07l-1.39-.82.5-1.53 2.03-6.14-3.98-1.69-3.05 5.8-.74 1.4-1.53-.4a31 31 0 0 0-3.61-.7l-1.59-.23-.13-1.6-.54-6.48h-4.32l-.54 6.49-.13 1.6zM42 43.5a14.5 14.5 0 1 1 0 29 14.5 14.5 0 0 1 0-29zM28.5 58a13.5 13.5 0 1 0 27 0 13.5 13.5 0 0 0-27 0zM52 58a10 10 0 1 1-20 0 10 10 0 0 1 20 0zm-2 0a8 8 0 1 0-16 0 8 8 0 0 0 16 0zM76.87 9.03l-1.53-.38c-.7-.17-1.38-.3-2.11-.41L71.66 8l-.13-1.58L71.16 2h-2.32l-.37 4.42L68.34 8l-1.57.24c-.73.1-1.4.24-2.11.41l-1.53.38-.73-1.39-2.08-3.95-2.13.9 1.37 4.2.5 1.51-1.37.82c-.63.38-1.2.77-1.8 1.2l-1.27.95-1.21-1.02-3.39-2.87-1.64 1.64 2.87 3.39 1.02 1.21-.94 1.27c-.44.6-.83 1.17-1.2 1.8l-.83 1.37-1.52-.5-2.55-.84.25 3.07 1.16.61 1.4.73-.39 1.52c.64.13 1.26.27 1.88.43l3.9-7.43 7.2 3.06a12.5 12.5 0 0 1 17.7 17.6l3 7.37-7.42 3.74c.16.64.31 1.28.44 1.93l1.55-.4.72 1.43.58 1.14 3.04.25-.8-2.6-.47-1.49 1.34-.8c.64-.39 1.2-.77 1.8-1.2l1.27-.95 1.21 1.02 3.39 2.87 1.64-1.64-2.87-3.39-1.02-1.21.94-1.27c.44-.6.83-1.17 1.2-1.8l.83-1.37 1.52.5 4.18 1.37.9-2.13-3.94-2.08-1.4-.73.39-1.52c.17-.71.3-1.39.41-2.12l.24-1.57 1.58-.13 4.42-.37v-2.32l-4.42-.37-1.58-.13-.24-1.57c-.1-.74-.24-1.41-.42-2.12l-.38-1.55 1.42-.72 3.94-1.99-.87-2.15-4.27 1.33-1.5.46-.8-1.34a21.2 21.2 0 0 0-1.2-1.8l-.95-1.27 1.02-1.21 2.87-3.39-1.64-1.64-3.39 2.87-1.21 1.02-1.27-.94c-.6-.44-1.17-.83-1.8-1.2l-1.37-.83.5-1.52L81.8 4.6l-2.13-.9-2.08 3.94-.73 1.4zM46.69 24.27l.02-.1-.03-.02v.12zm12.51 4.4c1.02.6 2 1.24 2.95 1.94l-.13-.1a8 8 0 1 1 7.47 7.47l-.02-.01c.66.9 1.27 1.82 1.84 2.78l5.97-1.85A11.5 11.5 0 1 0 61.2 22.59l-2 6.08zM76 30a6 6 0 0 0-11.99-.42l4.86-4.1 5.66 5.65-4.11 4.86A6 6 0 0 0 76 30zm-.14 23.32l-.02-.04-.1.03.12.01z"></path>
                    </svg>
                    <h2 class="section-title">
                        Execute custom commands on one instance, a selection or all instances
                    </h2>
                </div>
            </div>
            <div class="col px-2">
                <div class="card">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100" class="heroicon-code heroicon heroicon-lg">
                        <path class="heroicon-code-interface heroicon-component-fill" d="M0 18h100v59.997c0 2.21-1.8 4.003-3.997 4.003H3.997C1.79 82 0 80.205 0 77.997V18z"></path>
                        <path class="heroicon-code-side heroicon-component-fill" d="M1 17h7v64H4.01C2.346 81 1 79.663 1 78V17z"></path>
                        <path class="heroicon-code-menu-bar heroicon-component-accent heroicon-component-fill" d="M0 5.996C0 3.79 1.8 2 3.997 2h92.006C98.21 2 100 3.79 100 5.996V18H0V5.996z"></path>
                        <circle class="heroicon-code-circle heroicon-component-accent heroicon-component-fill" cx="82" cy="81" r="18"></circle>
                        <path class="heroicon-code-symbol heroicon-component-fill" d="M76.707 80.293L76 81l.707.707 3.586 3.586L81 86l-.707.707-1.586 1.586L78 89l-.707-.707-6.586-6.586L70 81l.707-.707 6.586-6.586L78 73l.707.707 1.586 1.586L81 76l-.707.707-3.586 3.586zm7-3.586L83 76l.707-.707 1.586-1.586L86 73l.707.707 6.586 6.586L94 81l-.707.707-6.586 6.586L86 89l-.707-.707-1.586-1.586L83 86l.707-.707 3.586-3.586L88 81l-.707-.707-3.586-3.586z"></path>
                        <path class="heroicon-outline" fill-rule="nonzero" d="M3.997 2h92.006C98.21 2 100 3.783 100 5.995v72.01c0 .328-.04.647-.115.952.076.67.115 1.352.115 2.043 0 9.94-8.06 18-18 18-9.606 0-17.454-7.524-17.973-17H3.997C1.79 82 0 80.217 0 78.005V5.995C0 3.788 1.8 2 3.997 2zM82 63c6.966 0 13.007 3.957 16 9.746V18H9v62h55.027c.52-9.476 8.367-17 17.973-17zm16-51V5.995C98 4.89 97.108 4 96.003 4H3.997C2.9 4 2 4.897 2 5.995V12h16l6-6h18l6 6h50zM2 14v2h96v-2H47.172l-6-6H24.828l-6 6H2zm0 4v60.005C2 79.11 2.892 80 3.997 80H7V18H2zm80 79c8.837 0 16-7.163 16-16s-7.163-16-16-16-16 7.163-16 16 7.163 16 16 16zM11 20h4v1h-4v-1zm14 0v1h-8v-1h8zm2 0h6v1h-6v-1zm20 0v1H35v-1h12zm-26 3v1H11v-1h10zm8 0v1h-6v-1h6zm10 0v1h-8v-1h8zm14 1H41v-1h12v1zm2-1h6v1h-6v-1zm-34 4h-8v-1h8v1zm2-1h6v1h-6v-1zm18 0v1H31v-1h10zm2 0h6v1h-6v-1zm16 1h-8v-1h8v1zm-40 2v1h-4v-1h4zm10 1h-8v-1h8v1zm2-1h10v1H31v-1zm24 1H43v-1h12v1zm10-1v1h-8v-1h8zm10 0v1h-8v-1h8zM25 56v1h-8v-1h8zm-4 4h-4v-1h4v1zm10-1v1h-8v-1h8zm12 0v1H33v-1h10zm2 0h10v1H45v-1zm-18-3h10v1H27v-1zm20 0v1h-8v-1h8zm8 0v1h-6v-1h6zm2 0h10v1H57v-1zm18 0v1h-6v-1h6zm2 0h10v1H77v-1zm19 0v1h-7v-1h7zM21 32v1h-6v-1h6zm12 0v1H23v-1h10zm-16 3h7v1h-7v-1zm5 18v1h-7v-1h7zm-7 9h9v1h-9v-1zm5 3v1h-5v-1h5zm-3 3h7v1h-7v-1zm16 0v1h-7v-1h7zm12 0v1H35v-1h10zm8 0v1h-6v-1h6zm-32 3v1h-6v-1h6zm-8 3h6v1h-6v-1zm4 3v1h-6v-1h6zm10 1h-8v-1h8v1zm10-1v1h-8v-1h8zm-9-11h-6v-1h6v1zm2-1h8v1h-8v-1zm0-27v1H19v-1h11zm2 0h8v1h-8v-1zm-7 3v1h-8v-1h8zm-10 3h6v1h-6v-1zm8 6v1h-8v-1h8zm6 0v1h-4v-1h4zm0-6v1h-6v-1h6zm-12 3h10v1H17v-1zm18 0v1h-6v-1h6zm2 0h4v1h-4v-1zm8-15v1H35v-1h10zM6 10c-1.105 0-2-.895-2-2s.895-2 2-2 2 .895 2 2-.895 2-2 2zm7-2c0 1.105-.895 2-2 2s-2-.895-2-2 .895-2 2-2 2 .895 2 2zm3 2c-1.105 0-2-.895-2-2s.895-2 2-2 2 .895 2 2-.895 2-2 2zM4 20h1v1H4v-1zm1 3v1H4v-1h1zm-1 3h1v1H4v-1zm1 3v1H4v-1h1zm-1 3h1v1H4v-1zm1 3v1H4v-1h1zm-1 3h1v1H4v-1zm1 3v1H4v-1h1zm-1 3h1v1H4v-1zm1 3v1H4v-1h1zm-1 3h1v1H4v-1zm1 3v1H4v-1h1zm-1 3h1v1H4v-1zm1 3v1H4v-1h1zm-1 3h1v1H4v-1zm1 3v1H4v-1h1zm-1 3h1v1H4v-1zm1 3v1H4v-1h1zm-1 3h1v1H4v-1zm1 3v1H4v-1h1zm63 4c0-7.732 6.268-14 14-14v1c-7.18 0-13 5.82-13 13 0 1.652.308 3.232.87 4.686l-.908.425C68.342 84.53 68 82.805 68 81zm8.707-.707L76 81l.707.707 3.586 3.586L81 86l-.707.707-1.586 1.586L78 89l-.707-.707-6.586-6.586L70 81l.707-.707 6.586-6.586L78 73l.707.707 1.586 1.586L81 76l-.707.707-3.586 3.586zM79.587 86l-4.294-4.293-.707-.707.707-.707L79.586 76 78 74.414 71.414 81 78 87.586 79.586 86zm4.12-9.293L83 76l.707-.707 1.586-1.586L86 73l.707.707 6.586 6.586L94 81l-.707.707-6.586 6.586L86 89l-.707-.707-1.586-1.586L83 86l.707-.707 3.586-3.586L88 81l-.707-.707-3.586-3.586zm5 5L84.414 86 86 87.586 92.586 81 86 74.414 84.414 76l4.293 4.293.707.707-.707.707z"></path>
                    </svg>
                    <h2 class="section-title">
                        Using a clean and documented PHP API to create list, forms, filters, searches, commands, ...
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="section my-3">
        <div class="card">
            <div class="row align-items-center">
                 <div class="col-3">
                      <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100" class="heroicon-devices heroicon heroicons-lg">
                          <path class="heroicon-devices-tablet-edge-outer heroicon-component-accent heroicon-component-fill" d="M6 2h68a4 4 0 0 1 4 4v22h-1V6a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v88a3 3 0 0 0 3 3h52.8c.21.36.45.7.73 1H6a4 4 0 0 1-4-4V6a4 4 0 0 1 4-4z"></path>
                          <path class="heroicon-devices-tablet-edge-inner heroicon-component-accent heroicon-component-fill" d="M58.34 96a5.99 5.99 0 0 1-.34-2v-5H7V11h66v17h3V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v88c0 1.1.9 2 2 2h52.34z"></path>
                          <path class="heroicon-devices-tablet-screen heroicon-component-fill" d="M72 28V12H8v76h50V34a6 6 0 0 1 6-6h8z"></path>
                          <path class="heroicon-devices-tablet-button heroicon-component-fill" d="M43.5 92h-7a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1z"></path>
                          <path class="heroicon-devices-phone-edge-outer heroicon-component-accent heroicon-component-fill" d="M64 30h30a4 4 0 0 1 4 4v60a4 4 0 0 1-4 4H64a4 4 0 0 1-4-4V34a4 4 0 0 1 4-4z"></path>
                          <path class="heroicon-devices-phone-edge-inner heroicon-component-accent heroicon-component-fill" d="M94 32H64a2 2 0 0 0-2 2v60c0 1.1.9 2 2 2h30a2 2 0 0 0 2-2V34a2 2 0 0 0-2-2z"></path>
                          <polygon class="heroicon-devices-phone-screen heroicon-component-fill" points="64 40 94 40 94 88 64 88"></polygon>
                          <path class="heroicon-devices-phone-button heroicon-component-fill" d="M75.5 92h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1z"></path>
                          <path class="heroicon-shadows" d="M64 28h-5a6 6 0 0 0-6 6v66h11v-.08A6 6 0 0 1 59 94V34a6 6 0 0 1 5-5.92V28z"></path>
                          <path class="heroicon-outline" fill-rule="nonzero" d="M64 100H6a6 6 0 0 1-6-6V6a6 6 0 0 1 6-6h68a6 6 0 0 1 6 6v22h14a6 6 0 0 1 6 6v60a6 6 0 0 1-6 6H64zM6 2a4 4 0 0 0-4 4v88a4 4 0 0 0 4 4h53.53c-.28-.3-.52-.64-.73-1H6a3 3 0 0 1-3-3V6a3 3 0 0 1 3-3h68a3 3 0 0 1 3 3v22h1V6a4 4 0 0 0-4-4H6zm52.34 94a5.99 5.99 0 0 1-.34-2v-5H7V11h66v17h3V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v88c0 1.1.9 2 2 2h52.34zM72 28V12H8v76h50V34a6 6 0 0 1 6-6h8zm-8 2a4 4 0 0 0-4 4v60a4 4 0 0 0 4 4h30a4 4 0 0 0 4-4V34a4 4 0 0 0-4-4H64zm0 1h30a3 3 0 0 1 3 3v60a3 3 0 0 1-3 3H64a3 3 0 0 1-3-3V34a3 3 0 0 1 3-3zm30 1H64a2 2 0 0 0-2 2v60c0 1.1.9 2 2 2h30a2 2 0 0 0 2-2V34a2 2 0 0 0-2-2zm-31 7h32v50H63V39zm1 1v48h30V40H64zM37 7h6v1h-6V7zm-.5 84h7a1.5 1.5 0 0 1 0 3h-7a1.5 1.5 0 0 1 0-3zm7 1h-7a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1zm.95-54.78l-10 20-.9-.44 10-20 .9.44zm1 6l-6 12-.9-.44 6-12 .9.44zm27.1 28.56l10-20 .9.44-10 20-.9-.44zm5.9-1.56l-.9-.44 6-12 .9.44-6 12zM74 92.5c0-.83.67-1.5 1.5-1.5h7a1.5 1.5 0 0 1 0 3h-7a1.5 1.5 0 0 1-1.5-1.5zm1.5-.5a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zM82 35v1h-6v-1h6z"></path>
                      </svg>
                 </div>
                 <div class="col">
                      <h2 class="section-title mt-0" style="text-align: left">
                          Taking advantage of custom Vue.js-based components for uploads and image cropping,
                          multiselects, lists, autocompletes, ... all responsive, and without having to write a
                          single line of front code.
                      </h2>
                 </div>
            </div>
        </div>
    </div>
</div>

Sharp intends to provide a clean solution to the following needs:
- create, update or delete any structured data of the project, handling validation and errors;
- display, search, sort or filter data;
- execute custom commands on one instance, a selection or all instances;
- handle authorizations and validation;
- all without write a line of front code, and using a clean API in the PHP app.

<br>

![Dashboard](./img/dashboard.png)

![Entity form](./img/form.png)

![Entity list](./img/list.png)