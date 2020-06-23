
<div role="alert" class="SharpNotification SharpNotification--info">
    <div class="SharpNotification__details">
        <div class="SharpNotification__text-wrapper">
            <div class="SharpNotification__title mb-2">
                Use these accounts to login:
            </div>
            <p class="SharpNotification__subtitle">
                <ul>
                    <li class="mb-2">
                        <input
                            class="SharpButton SharpButton--sm SharpButton--secondary"
                            type="button"
                            onclick="this.form.elements.login.value='admin@example.com';this.form.elements.password.value='secret'"
                            value="Admin account"
                        >
                    </li>
                    <li>
                        <input
                            class="SharpButton SharpButton--sm SharpButton--secondary"
                            type="button"
                            onclick="this.form.elements.login.value='boss@example.com';this.form.elements.password.value='secret'"
                            value="Boss account"
                        >
                        <span style="font-style: italic">(has a few more permissions)</span>
                    </li>
                </ul>
            </p>
        </div>
    </div>
</div>
