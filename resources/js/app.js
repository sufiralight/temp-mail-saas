const themeToggle = document.querySelector('[data-theme-toggle]');
const systemTheme = window.matchMedia('(prefers-color-scheme: dark)');

function updateTheme(theme) {
    document.documentElement.dataset.theme = theme;
    themeToggle.setAttribute('aria-pressed', String(theme === 'dark'));
}

themeToggle.hidden = false;
updateTheme(document.documentElement.dataset.theme);

themeToggle.addEventListener('click', () => {
    const theme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    updateTheme(theme);

    try {
        localStorage.setItem('temp-mail-theme', theme);
    } catch {
        // The theme still works for this page when storage is unavailable.
    }
});

systemTheme.addEventListener('change', ({ matches }) => {
    try {
        if (localStorage.getItem('temp-mail-theme')) {
            return;
        }
    } catch {
        // Fall back to the system preference.
    }

    updateTheme(matches ? 'dark' : 'light');
});

document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    const input = document.getElementById(button.getAttribute('aria-controls'));
    button.hidden = false;

    button.addEventListener('click', () => {
        const visible = input.type === 'password';
        input.type = visible ? 'text' : 'password';
        button.setAttribute('aria-pressed', String(visible));
        button.textContent = visible ? 'Hide' : 'Show';
    });
});

document.querySelectorAll('[data-pending-form]').forEach((form) => {
    const submit = form.querySelector('[type=submit]');
    const status = document.getElementById(form.dataset.pendingForm);

    form.addEventListener('submit', (event) => {
        if (submit.disabled) {
            event.preventDefault();
            return;
        }

        submit.disabled = true;
        submit.querySelector('[data-submit-label]').textContent = submit.dataset.pendingLabel;
        submit.querySelector('[data-spinner]').hidden = false;
        status.textContent = submit.dataset.pendingLabel;
    });
});

document.querySelector('[data-error-summary]')?.focus();

window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        window.location.reload();
    }
});
