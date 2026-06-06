document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-auto-filter]').forEach((form) => {
        let timeoutId = null;

        const submitForm = () => {
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
                return;
            }

            form.submit();
        };

        const submitWithDelay = () => {
            window.clearTimeout(timeoutId);
            timeoutId = window.setTimeout(submitForm, 450);
        };

        form.querySelectorAll('input[name="search"], input[data-auto-filter-input]').forEach((input) => {
            input.addEventListener('input', submitWithDelay);
        });

        form.querySelectorAll('select, input[type="date"]').forEach((input) => {
            input.addEventListener('change', submitForm);
        });
    });
});
