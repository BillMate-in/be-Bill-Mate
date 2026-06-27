document.addEventListener('DOMContentLoaded', () => {
    const filterButtons = document.querySelectorAll('.flex.gap-sm button');

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            filterButtons.forEach(btn => {
                btn.classList.remove('active-pill');
                btn.classList.add(
                    'bg-surface-container-lowest',
                    'border',
                    'border-surface-variant',
                    'text-secondary'
                );
            });

            button.classList.add('active-pill');
            button.classList.remove(
                'bg-surface-container-lowest',
                'border',
                'border-surface-variant',
                'text-secondary'
            );
        });
    });
});