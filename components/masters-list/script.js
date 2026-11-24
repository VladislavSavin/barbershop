document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.master-card-about');
    const modal = document.getElementById('masterModal');
    const overlay = document.getElementById('masterModalOverlay');
    const closeBtn = document.getElementById('masterModalClose');

    const modalName = document.getElementById('modalMasterName');
    const modalSpec = document.getElementById('modalMasterSpec');
    const modalAvatar = document.getElementById('modalMasterAvatar');

    if (!modal) return;

    // Открытие
    cards.forEach(card => {
        card.addEventListener('click', () => {
            const name = card.getAttribute('data-name');
            const spec = card.getAttribute('data-spec');
            const avatar = card.getAttribute('data-avatar');

            modalName.textContent = name;
            modalSpec.textContent = spec;

            if (avatar) {
                modalAvatar.innerHTML = `<img src="${avatar}" alt="${name}">`;
            } else {
                modalAvatar.innerHTML = `<div class="avatar-placeholder-modal">${name.charAt(0)}</div>`;
            }

            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    // Закрытие
    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (overlay) overlay.addEventListener('click', closeModal);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });
});