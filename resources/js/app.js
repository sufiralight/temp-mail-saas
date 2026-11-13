const themeControl = document.querySelector('[data-theme-control]');
const themeTrigger = themeControl.querySelector('[data-theme-trigger]');
const themeMenu = themeControl.querySelector('[role=menu]');
const themeOptions = [...themeMenu.querySelectorAll('[data-theme-mode]')];
const themeIcons = themeTrigger.querySelectorAll('[data-theme-icon]');
const themeKey = 'temp-mail-theme';
const systemTheme = window.matchMedia('(prefers-color-scheme: dark)');

function savedTheme() {
    try {
        const theme = localStorage.getItem(themeKey);
        return theme === 'light' || theme === 'dark' ? theme : 'system';
    } catch {
        return 'system';
    }
}

let themeMode = savedTheme();

function applyTheme() {
    document.documentElement.dataset.theme = themeMode === 'system'
        ? (systemTheme.matches ? 'dark' : 'light')
        : themeMode;

    themeOptions.forEach((option) => {
        option.setAttribute('aria-checked', String(option.dataset.themeMode === themeMode));
    });

    themeIcons.forEach((icon) => {
        icon.toggleAttribute('hidden', icon.dataset.themeIcon !== themeMode);
    });

    const label = `Appearance: ${themeOptions.find((option) => option.dataset.themeMode === themeMode).textContent.trim()}`;
    themeTrigger.setAttribute('aria-label', label);
    themeTrigger.title = label;
}

function openThemeMenu(option) {
    themeMenu.hidden = false;
    themeTrigger.setAttribute('aria-expanded', 'true');
    option.focus();
}

function closeThemeMenu(restoreFocus = false) {
    themeMenu.hidden = true;
    themeTrigger.setAttribute('aria-expanded', 'false');

    if (restoreFocus) {
        themeTrigger.focus();
    }
}

applyTheme();
themeTrigger.hidden = false;

themeTrigger.addEventListener('click', () => {
    if (themeMenu.hidden) {
        openThemeMenu(themeOptions.find((option) => option.dataset.themeMode === themeMode));
    } else {
        closeThemeMenu(true);
    }
});

themeControl.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !themeMenu.hidden) {
        event.preventDefault();
        closeThemeMenu(true);
        return;
    }

    const focusedIndex = themeOptions.indexOf(document.activeElement);
    const lastIndex = themeOptions.length - 1;
    let nextIndex;

    switch (event.key) {
        case 'ArrowDown':
            nextIndex = (focusedIndex + 1) % themeOptions.length;
            break;
        case 'ArrowUp':
            nextIndex = focusedIndex <= 0 ? lastIndex : focusedIndex - 1;
            break;
        case 'Home':
            nextIndex = 0;
            break;
        case 'End':
            nextIndex = lastIndex;
            break;
        default:
            return;
    }

    event.preventDefault();
    openThemeMenu(themeOptions[nextIndex]);
});

themeControl.addEventListener('focusout', (event) => {
    if (event.relatedTarget === themeTrigger || !themeControl.contains(event.relatedTarget)) {
        closeThemeMenu();
    }
});

document.addEventListener('pointerdown', (event) => {
    if (!themeMenu.hidden && !themeControl.contains(event.target)) {
        closeThemeMenu();
    }
});

themeOptions.forEach((option) => {
    option.addEventListener('click', () => {
        themeMode = option.dataset.themeMode;
        applyTheme();
        closeThemeMenu(true);

        try {
            if (themeMode === 'system') {
                localStorage.removeItem(themeKey);
            } else {
                localStorage.setItem(themeKey, themeMode);
            }
        } catch {
            // The theme still works for this page when storage is unavailable.
        }
    });
});

systemTheme.addEventListener('change', applyTheme);
window.addEventListener('storage', ({ key }) => {
    if (key === themeKey || key === null) {
        themeMode = savedTheme();
        applyTheme();
    }
});

document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    const input = document.getElementById(button.getAttribute('aria-controls'));
    button.hidden = false;

    button.addEventListener('click', () => {
        const visible = input.type === 'password';
        input.type = visible ? 'text' : 'password';
        button.setAttribute('aria-pressed', String(visible));
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
        submit.querySelector('[data-submit-arrow]')?.setAttribute('hidden', '');
        status.textContent = submit.dataset.pendingLabel;
    });
});

document.querySelector('[data-error-summary]')?.focus();

window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        window.location.reload();
    }
});
