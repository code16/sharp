<div class="flex flex-wrap gap-2">
    @foreach($tags as $tag)
        <x-sharp::tag
            :label="$tag['label']"
            :url="$tag['url']"
            :label-limit="$labelLimit"
        />
    @endforeach
</div>
