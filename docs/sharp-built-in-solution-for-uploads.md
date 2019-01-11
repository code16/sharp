# Sharp built-in solution for uploads

Uploads are painful.

Sharp provide a very opinionated and totally optional solution to handle if you are using Eloquent and the `WithSharpFormEloquentUpdater` trait (see [related documentation](building-entity-form.md)).

The proposal is to use a special Sharp Model for all your uploads, and to link them to your Models with Eloquent's Morph relationships.

## Use `SharpUploadModel`

The base Model class is `Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel`. Just create your own Model class and make it extends this base class.

You'll have to define the Eloquent `$table` attribute to indicate the table name. So for instance, let's say your Model name choice is `Media`, here's the class code:

```php
    use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;

    class Media extends SharpUploadModel
    {
        protected $table = "medias";
    }
```

## Create the migration

Sharp provides an artisan command for that:
`sharp:create_uploads_migration <table_name>`

Pass your specific table name in the `table_name` argument ("medias" in our example).

This command will create a migration file like this one:

```php
    class CreateMediasTable extends Migration
    {
        public function up()
        {
            Schema::create('medias', function (Blueprint $table) {
                $table->increments('id');
                $table->morphs('model');
                $table->string('model_key')->nullable();
                $table->string('file_name')->nullable();
                $table->string('mime_type')->nullable();
                $table->string('disk')->default('local')->nullable();
                $table->unsignedInteger('size')->nullable();
                $table->text('custom_properties')->nullable();
                $table->unsignedInteger('order')->nullable();
                $table->timestamps();
            });
        }
        
        public function down()
        {
            Schema::dropIfExists('medias');
        }
    }
```

## Link to your Models

Now, you need to define the relationships. Let's say you have a Book model, and you want the user to be able to upload its cover and PDF version.

```php
    class Book extends Model
    {
	    public function cover()
        {
            return $this->morphOne(Media::class, "model")
                ->where("model_key", "cover");
        }

        public function pdf()
        {
            return $this->morphOne(Media::class, "model")
                ->where("model_key", "pdf");
        }
    }
```

## Use it!

Let's pretend you already have data in this new table, here how to handle it.

### Properties

By default, you can get the `file_name`, but also `mime_type` and file's `size`.


### Custom properties

You can add whatever property you need through custom properties, by setting it:

`$book->cover->author = "Tomi Ungerer";`

Custom properties will be stored in the `custom_properties` column, as JSON.

You can retreive the value the same way:

`$author = $book->cover->author`


### Thumbnails

Thumbnail creation, for image, is built-in, with this function:
`thumbnail($width=null, $height=null, $filters=[])`

You must first define the thumbnail directory, in Sharp's config:

```php
    // config/sharp.php
    
    "uploads" => [
        "thumbnails_dir" => "thumbnails",
    ],
```

This path is relative to the `public` directory.

Then you can call `$thumb = $book->cover->thumbnail(150)` to have a full URL to a 150px (width) thumbnail.

#### Filters

The third argument is for Filters. For now, only two are available:

- **greyscale**
`->thumbnail(150, null, ["greyscale" => []])`


- **fit**: this one has 2 params, `w` for width and `h` for height, and will center-fit the image in those constraints.
`->thumbnail(150, null, ["fit" => ["w"=>150, "h"=>100]])`

But of course you can provide here a custom one. You'll need for that to first create a Filter class that extends `Code16\Sharp\Form\Eloquent\Uploads\Thumbnails\ThumbnailFilter`, implementing:

- `function applyFilter(Intervention\Image\Image $image)`: apply you filter, using the great [Intervention API](http://image.intervention.io).
- `function resized()`: (optional, default to false) Return true if the resize is part of the `applyFilter()` code.

Once the class is created, simply pass the full class path as filter name:

```php
    return $this->thumbnail($size, $size, [
        CustomThumbnailFilter::class => ["w"=>$w, 'fill'=>'#ffffff']
    ]);
```

## Update with Sharp

The best part is this: Sharp will take care of everything related to update and store.

First declare your upload, like usual:

```php
    function buildFormFields()
    {
        $this->addField(
            SharpFormUploadField::make("cover")
                ->setLabel("Cover")
                ->setFileFilterImages()
                ->setCropRatio("1:1")
                ->setStorageDisk("local")
                ->setStorageBasePath("data/Books")
            )
        );
    }
```

Then add a customTransformer:

```php
    function find($id): array
    {
        return $this->setCustomTransformer(
            "cover", 
            new FormUploadModelTransformer()
        )->transform(
            Book::with("cover")->findOrFail($id)
        );
    }
```

The full path of this transformer is `Code16\Sharp\Form\Eloquent\Transformers\FormUploadModelTransformer`.

And finally, and this is a sad exception to the "don't touch the applicative code for Sharp", add this in your Model that declares an upload relationship (Book, in our example):

```php
    public function getDefaultAttributesFor($attribute)
    {
        return in_array($attribute, ["cover"])
            ? ["model_key" => $attribute]
            : [];
    }
```

This will tell SharpEloquentUpdater to add the necessary `model_key`attribute when creating a new upload.

And... voilà! From there, Sharp will handle the rest.

### Updating custom attributes

So we want to add an `author` custom attribute to our cover field. It's very easy, add the field in the Sharp Entity Form, using the `:` separator to designate a related attribute:

```php
    $this->addField(
        SharpFormTextField::make("cover:author")
            ->setLabel("Author")
    );
```

Here we intend to update the `author` attribute of the `cover` relation.


## What about upload lists?

So let's say we want to add pictures of inner pages, for our Book. It can be easily done by creating a `morphMany` relation in the Book Model:

```php
    public function pictures()
    {
        return $this->morphMany(Media::class, "model")
            ->where("model_key", "pictures")
            ->orderBy("order");
    }
```

And then add the field in the Sharp Entity Form:

```php
    $this->addField(
        SharpFormListField::make("pictures")
            ->setLabel("Additional pictures")
            ->setAddable()->setAddText("Add a picture")
            ->setRemovable()
            ->setSortable()
            ->setOrderAttribute("order")
            ->addItemField(
                SharpFormUploadField::make("file")
                    ->setFileFilterImages()
                    ->setStorageDisk("local")
                    ->setStorageBasePath("data/Books/Pictures")
            )
        )
    );
```

Note that we use the a special `file` key for the SharpFormUploadField in the item.

You'll have next to update your Model special `getDefaultAttributesFor()` function:

```php
    public function getDefaultAttributesFor($attribute)
    {
        return in_array($attribute, ["cover","pictures"])
            ? ["model_key" => $attribute]
            : [];
    }
```

All set.

#### Updating custom attributes in upload lists

```php
    $this->addField(
        SharpFormListField::make("pictures")
            [...]
            ->addItemField(
                SharpFormUploadField::make("file")
                    [...]
            )->addItemField(
                SharpFormTextField::make("legend")
            )
        )
    );
```

In this code, the `legend` designates a custom attribute.

---

> Next chapter : [Form data localization](form-data-localization.md)