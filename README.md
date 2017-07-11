# Sharp 4

> Sharp is under heavy development. It should be out and fully documented in a few weeks.

Sharp is not a CMS: it's a content management framework, a toolset which provides help building a CMS section in a website, with some rules in mind:
- the public website should not have any knowledge of the CMS;
- the CMS should not have any expectations from the persistence layer (meaning: the DB structure has nothing to do with the CMS);
- in fact, removing the CMS should not have any effect on the project (well, ok, except for the administrator);
- administrators should work with their data and terminology, not CMS terms;
- website developers should not have to work on the front-end development for the CMS. 
 
Sharp intends to provide a clean solution to the following needs:
- create, update or delete any structured data of the project, handling validation and errors;
- display, search, sort or filter data;
- execute custom commands on one instance, a selection or all instances;
- handle authorizations and validation;
- all without write a line of front code, and using a clean API in the PHP app.

Sharp 4 needs Laravel 5.4+ and PHP 7.0+.

## Terminology, general concept

In Sharp, we handle `entities`; and `entity` is simply a data structure which has a meaning in the applicative context. For instance, a `Person`, a `Post` or an `Order`. In the Eloquent world, for which Sharp is optimized, it's typically a Model — but it's not necessarily a 1-1 relationship, a Sharp `entity` can represent a portion of a Model, or several Models.

Each instance of an `entity` is called... an `instance`.

Each `entity` in Sharp can be displayed:
- in an `Entity List`, which is the list of all the `instances` for this `entity`: with some configuration and code, the user can sort the data, add filters, and perform a search. From there we also gain access to applicative `commands` applied to an `instance` or the whole list, and to a simple `state` changer (the publish state of an Article, for instance). All of that is described below.
- And in a `Form`, either to update or create a new `instance`.

## Installation

- Add the package with composer: `composer require code16/sharp`,
- Register the service provider `Code16\Sharp\SharpServiceProvider` in the provider array of `config/app.php`,
- Publish assets: `php artisan vendor:publish --provider=Code16\Sharp\SharpServiceProvider`.

## Configuration

Sharp needs a `config/sharp.php` config file, mainly to declare `entities`. Here's a simple example:

    return [
        "entities" => [
            "spaceship" => [
                "list" => \App\Sharp\SpaceshipSharpList::class,
                "form" => \App\Sharp\SpaceshipSharpForm::class,
                "validator" => \App\Sharp\SpaceshipSharpValidator::class,
                "policy" => \App\Sharp\Policies\SpaceshipPolicy::class
            ]
        ]
    ];

As we can see, each `entity` (like `spaceship`, here), can define:

- a `list` class, responsible for the `Entity List`,
- a `form` class, responsible for... the `Form`
- and optionally:
	- a `validator` class, to handle form validation
	- and a `policy` class, for authorization.

We'll get into all those classes in this document. The important thing to notice is that Sharp provides base classes to handle all the wiring (and more), but as we'll see, the applicative code is totally up to you.

## Building an Entity List

Let's start with the applicative code needed to show the list of `instances` for an `entity`. In the example shown in the configuration excerpt above, it's the `\App\Sharp\SpaceshipSharpList` class. We need to make it extend `Code16\Sharp\EntityList\SharpEntityList`, and therefore to implement 4 abstract methods:

### `buildListDataContainers()`

A "data container" is simply a column in the `Entity List`, named this way to abstract the presentation. This first function is responsible to describe each column:

    function buildListDataContainers()
    {
        $this->addDataContainer(
	        EntityListDataContainer::make("name")
			    ->setLabel("Full name")
			    ->setSortable()
			    ->setHtml()
	    )->addDataContainer([...]);
    }

Setting the label, allowing the column to be sortable and to display html is optionnal.

### `buildListLayout()`

Next step, define how those columns are displayed:

    function buildListLayout()
    {
        $this->addColumn("picture", 1, 2)
            ->addColumn("name", 9, 10)
            ->addColumnLarge("capacity", 2);
    }

We add columns giving:

- the column key, which must have been defined in `buildListDataContainers()`,
- the "width" of the column, as an integer on a 12-based grid,
- and the 2nd integer in the width on small screen.

In this example, `picture` and `name` will be displayed respectively on 1/12 and 9/12 of the viewport width on large screens, and 2/12 and 10/12 on small screens. The third column, `capacity`, will only be shown on large screens, with a width of 2/12.

### `getListData(EntityListQueryParams $params)`

Now the real work: grab and return the actual list data. This method must return an array of `instances` of our `entity`. You can do this however you want, so let's see a generic example, and then how Sharp can simplify the work if you use Eloquent.


#### The generic way

Simply return an array, with 2 rules:

- each item must define the keys declared in the `buildDatacontainer()` function,
- plus one attribute for the identifier, which is `id` by default (more on that later).

So for instance, if we defined 3 columns:

    function getListData(EntityListQueryParams $params)
    {
	    return [
            [
    			"id" => 1,
    			"name" => "USS Enterprise",
    			"capacity" => "10k"
    		], [
    			"id" => 2,
    			"name" => "USS Agamemnon",
    			"capacity" => "20k"			
    		]
    	];
    }

Of course, real code will imply some data request, in a DB, a file, ... The important thing is that Sharp won't care.


#### Transformers

In a more realistic project, you'll want to transform your data before sending it to the front code. Sharp can help: first use the  `Code16\Sharp\Utils\Transformers\WithCustomTransformers` trait in your class, and then you gain access to:

    function getListData(EntityListQueryParams $params)
    {
	    // Sudo code to retreive instances.
	    $spaceships = $this->repository->all();
	    
	    return $this->setCustomTransformer(
		    "capacity", 
		    function($spaceship) {
                return (spaceship->capacity/1000) . "k";
            })
        )->transform($spaceships);
    }

The `setCustomTransformer()` function takes the key of the attribute to transform, and either a `Closure` or the full name of a class which must implement `Code16\Sharp\Utils\Transformers\SharpAttributeTransformer`.

The `transform` function must be called 

> Note that transformers need your models (spaceships, here) to allow a direct access to their attributes, like for instance `spaceship->capacity`.


#### The : operator

#### Handle query `$params`


#### The Eloquent way






#### Pagination



### `buildListConfig()`


