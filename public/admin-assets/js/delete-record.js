(function () {
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.delete-record');
        if (!btn) return;

        const url = btn.getAttribute('data-url');
        const id = btn.getAttribute('data-id') || '';
        if (!url) return;

        if (!confirm('هل أنت متأكد من الحذف؟')) return;

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        try {
            const res = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await res.json().catch(() => ({}));
            if (res.ok && data?.status) {
                // Remove row if inside a table, otherwise reload as fallback
                const row = btn.closest('tr');
                if (row) {
                    row.remove();
                } else {
                    location.reload();
                }
            } else {
                alert(data?.message || 'تعذر الحذف، حاول مرة أخرى.');
            }
        } catch (err) {
            console.error(err);
            alert('حدث خطأ غير متوقع.');
        }
    }, false);
})();
