


<x-sharp::form :form="$form">
    <x-sharp::form.text-field
        name="name"
        localized
        :max-length="30"
    >
        <x-slot name="label">
            Name
        </x-slot>
    </x-sharp::form.text-field>
</x-sharp::form>
