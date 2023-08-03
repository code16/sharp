
<div class="bg-white rounded p-4 text-center">
    <div class="text-sm mb-2">
        Use these accounts to login&nbsp;:
    </div>
    <button
        class="rounded-md bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-600 shadow-sm hover:bg-indigo-100"
        type="button"
        onclick="fillForm('admin@example.org')"
    >
        Admin
    </button>
    <button
        class="rounded-md bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-600 shadow-sm hover:bg-indigo-100"
        type="button"
        onclick="fillForm('editor@example.org')"
    >
        Editor
    </button>
</div>

@push('script')
    <script>
        function fillForm(email) {
            document.querySelector('#login').value = email;
            document.querySelector('#password').value = 'password';
        }

        window.addEventListener('sharp:mounted', () => {
            fillForm('admin@example.org');
        });
    </script>
@endpush
