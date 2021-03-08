
<div role="alert" class="SharpNotification SharpNotification--info">
    <div class="SharpNotification__details">
        <div class="SharpNotification__text-wrapper">
            <div class="SharpNotification__title mb-3">
                Use these accounts to login:
            </div>
            <div class="SharpNotification__subtitle">
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
        </div>
    </div>
</div>
