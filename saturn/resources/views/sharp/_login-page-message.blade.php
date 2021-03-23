
<div class="card small border-0">
    <div class="card-body">
        <div class="fw-bold mb-2">
            Use these accounts to login&nbsp;:
        </div>
        <ul class="list-unstyled mb-0">
            <li class="mb-2">
                <input
                    class="btn btn-text btn-sm"
                    type="button"
                    onclick="this.form.elements.login.value='admin@example.com';this.form.elements.password.value='secret'"
                    value="Admin account"
                >
            </li>
            <li>
                <input
                    class="btn btn-text btn-sm"
                    type="button"
                    onclick="this.form.elements.login.value='boss@example.com';this.form.elements.password.value='secret'"
                    value="Boss account"
                >
                <span class="fst-italic text-muted">(has a few more permissions)</span class>
            </li>
        </ul>
    </div>
</div>
