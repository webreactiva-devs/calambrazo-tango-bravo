function orderedBy(field) {
    const form = document.getElementById('order_form');
    const current = document.getElementById('order_by').value;
    const order = document.getElementById('order').value;

    document.getElementById('order_by').value = field;
    document.getElementById('order').value = (current === field && order === 'asc') ? 'desc' : 'asc';
    document.getElementById('pageInput').value = 1;
    form.submit();
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.pagination-links a').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const page = new URL(this.href).searchParams.get('page');
            if (page){
                document.getElementById('pageInput').value = page;
                document.getElementById('order_form').submit();
            }
        });
    });
});

window.orderedBy = orderedBy;
