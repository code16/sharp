
<div role="alert" class="alert alert-info">
    <div class="fw-bold mb-2">
        Use these accounts to login:
    </div>
    <ul class="list-unstyled mb-0">
        <li class="mb-2">
            <input
                class="btn btn-light btn-sm"
                type="button"
                onclick="this.form.elements.login.value='admin@example.com';this.form.elements.password.value='secret'"
                value="Admin account"
            >
        </li>
        <li>
            <input
                class="btn btn-light btn-sm"
                type="button"
                onclick="this.form.elements.login.value='boss@example.com';this.form.elements.password.value='secret'"
                value="Boss account"
            >
            <span style="font-style: italic">(has a few more permissions)</span>
        </li>
    </ul>
</div>
