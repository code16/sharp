# Building the menu

The Sharp side menu can contain several links. Its organization is totally up to you, and is defined in the `sharp.php` config file.

<img width="150" src="./img/menu.png" alt="Menu">

All links shares common things:

- an icon (from [Font Awesome 4.7](https://fontawesome.com/v4.7.0/icons/))
- a label
- and an URL

Links can be grouped in categories, like Company, Travels and Admin in this example.

## Link to an entity list

```php
// sharp.php

[...]
"menu" => [
    [
        "label" => "Features",
        "icon" => "fa-superpowers",
        "entity" => "feature"
    ]
]
```

The `entity` value must correspond to some entity key described in the same `sharp.php` file.

## Link to a single entity show

```php
// sharp.php

[...]
"menu" => [
    [
        "label" => "Account",
        "icon" => "fa-user",
        "entity" => "account",
        "single" => true
    ]
]
```

The `single => true` attribute would mean Sharp will create a link towards a `SharpSingleShow` implementation for the entity `account`. See [doc related to Shows](TODO link). 

## Link to a dashboard

Very similar to entity lists, except that `entity` is replaced by a `dashboard` attribute which must contain a valid dashboard key:

```php
// sharp.php

[...]
"menu" => [
    [
        "label" => "Dashboard",
        "icon" => "fa-dashboard",
        "dashboard" => "company_dashboard"
    ]
]
```

## Link to an external URL

```php
// sharp.php

[...]
"menu" => [
    [
        "label" => "Some external link",
        "icon" => "fa-globe",
        "url" => "https://google.com"
        "new_window" => false,
    ]
]
```

## Group links in categories

Categories are groups that can be collapsed

```php
"menu" => [
    [
        "label" => "Company",
        "entities" => [
            [
                "label" => "Dashboard",
                "icon" => "fa-dashboard",
                "dashboard" => "company_dashboard"
            ],
            [
                "label" => "Spaceships",
                "icon" => "fa-space-shuttle",
                "entity" => "spaceship"
            ],
            [...]
        ]
    ]
]
```

## Add separators in categories

You can add a simple labelled separator in categories, as sub-categories

```php
"menu" => [
    [
        "label" => "Company",
        "entities" => [
            [
                "label" => "Dashboard",
                "icon" => "fa-dashboard",
                "dashboard" => "company_dashboard"
            ],
            [
                "separator" => true,
                "label" => "Separator",
            ],
            [
                "label" => "Spaceships",
                "icon" => "fa-space-shuttle",
                "entity" => "spaceship"
            ],
            [...]
        ]
    ]
]
```
