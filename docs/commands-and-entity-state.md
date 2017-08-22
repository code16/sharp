# Commands

Commands in Sharp are a powerful way to integrate functional processes in the content management. They can be used for instance to re-send an order to the customer, on synchronize pictures of a product, or preview a page...

## Write the Command class

First we need to write a class for our Command. It must extend the `Code16\Sharp\EntityList\Commands\EntityCommand` abstract class (for "entity commands", more on that below), and implement two functions. 

First one is `label(): string`, and must simply return the text label of the Command, displayed to the user:

    /**
     * @return string
     */
    public function label(): string
    {
        return "Reload full list";
    }

The second one, `execute(array $params = []): array` handles the work of the Command itself:

    /**
     * @param array $params
     * @return array
     */
    public function execute(array $params = []): array
    {
        return $this->reload();
    }

More on this `return $this->reload();` below.


### Command scope: instance or entity

The example above is an "entity" case: Command applies to a subset of entities, or all of them.


### Command return types

## Configure the Command

## Handle authorizations

### Authorizations for entity commands

### Authorizations for instance commands