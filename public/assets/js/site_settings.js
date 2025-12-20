(function () {
    function uid() {
        return 'new_' + Date.now() + '_' + Math.floor(Math.random() * 100000);
    }

    console.log("ddd")
    function setDisabled(el, disabled) {
        if (!el) return;
        el.disabled = disabled;
    }

    function setTypeUI(card, type) {
        const holder = card.querySelector('.value-holder');

        const inputText = card.querySelector('.value-text');
        const textarea = card.querySelector('.value-longtext');

        const fileWrap = card.querySelector('.value-file');
        const fileInput = card.querySelector('.file-input');
        const existingFilePath = card.querySelector('.existing-file-path');

        const switchedToFile = card.querySelector('.switched-to-file');

        const prevType = card.getAttribute('data-prev-type') || holder.dataset.type;

        holder.dataset.type = type;

        // Hide all
        if (inputText) inputText.style.display = 'none';
        if (textarea) textarea.style.display = 'none';
        if (fileWrap) fileWrap.style.display = 'none';

        // Disable all (IMPORTANT)
        setDisabled(inputText, true);
        setDisabled(textarea, true);
        setDisabled(fileInput, true);
        setDisabled(existingFilePath, true);

        if (type === 'text') {
            if (inputText) inputText.style.display = '';
            setDisabled(inputText, false);
        }

        if (type === 'longtext') {
            if (textarea) textarea.style.display = '';
            setDisabled(textarea, false);
        }

        if (type === 'file') {
            if (fileWrap) fileWrap.style.display = '';
            setDisabled(fileInput, false);
            setDisabled(existingFilePath, false);
        }

        // Clear incompatible values (your agreed rules)
        if (prevType !== type) {
            if ((prevType === 'text' || prevType === 'longtext') && type === 'file') {
                if (inputText) inputText.value = '';
                if (textarea) textarea.value = '';
                if (existingFilePath) existingFilePath.value = '';
                if (switchedToFile) switchedToFile.value = '1';
            }

            if (prevType === 'file' && (type === 'text' || type === 'longtext')) {
                if (existingFilePath) existingFilePath.value = '';
                if (fileInput) fileInput.value = '';
            }
        }

        card.setAttribute('data-prev-type', type);
    }

    function bindCard(card, isNew) {
        const typeSelect = card.querySelector('.type-select');
        const deleteBtn = card.querySelector('.delete-setting-btn');

        // init prev type
        const initialType = typeSelect ? typeSelect.value : 'text';
        card.setAttribute('data-prev-type', initialType);
        setTypeUI(card, initialType);

        if (typeSelect) {
            typeSelect.addEventListener('change', function () {
                setTypeUI(card, this.value);
            });
        }

        if (deleteBtn) {
            deleteBtn.addEventListener('click', function () {
                const id = card.getAttribute('data-id');

                if (!id) {
                    // new row => remove directly
                    card.remove();
                    return;
                }

                // existing row => mark deleted (append hidden input deleted_ids[])
                const form = document.getElementById('settings-form');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'deleted_ids[]';
                input.value = id;
                form.appendChild(input);

                card.remove();
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const addBtn = document.getElementById('add-setting-btn');
        const wrapper = document.getElementById('settings-wrapper');
        const tpl = document.getElementById('setting-template');

        // bind existing
        document.querySelectorAll('.setting-card').forEach(card => bindCard(card, false));

        if (addBtn) {
            addBtn.addEventListener('click', function () {
                const rowKey = uid();
                const html = tpl.innerHTML.replaceAll('__ROWKEY__', rowKey);
                const div = document.createElement('div');
                div.innerHTML = html.trim();

                const card = div.firstElementChild;
                wrapper.prepend(card);
                bindCard(card, true);
            });
        }
    });
})();
