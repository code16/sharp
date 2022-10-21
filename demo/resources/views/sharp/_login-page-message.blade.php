
<div class="card small border-0 text-center">
    <div class="card-body">
        <div class="fw-bold mb-2">
            Use these accounts to login&nbsp;:
        </div>
        <button
            class="btn btn-text btn-sm"
            type="button"
            onclick="fillForm('admin@example.org')"
        >
            Admin
        </button>
        <button
            class="btn btn-text btn-sm"
            type="button"
            onclick="fillForm('editor@example.org')"
        >
            Editor
        </button>
    </div>
</div>

@push('script')
    <script>
        function fillForm(email) {
            document.querySelector('#login').value = email;
            document.querySelector('#password').value = 'password';
        }

        fillForm('admin@example.org');
    </script>
@endpush
