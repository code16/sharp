# Artisan Generators

For more information on each command and its options & arguments run `php artisan <command> --help`

```sh
# Generate a sharp entity form
php artisan sharp:make:form <class name> [--model=<model name>]

# Generate a sharp entity list
php artisan sharp:make:list

# Generate a list command
php artisan sharp:make:list-command <class name> [--model=<model name>]

# Generate a list filter
php artisan sharp:make:list-filter <class name> [--required,--multiple]

# Generate a reorder handler
php artisan sharp:make:reorder-handler <class name> [--model=<model name>]

# Generate a model state
php artisan sharp:make:state <class name> [--model=<model name>]

# Run a wizard that will create a model, list & form in one go
php artisan sharp:model-wizard [--model=<model name>]
```
