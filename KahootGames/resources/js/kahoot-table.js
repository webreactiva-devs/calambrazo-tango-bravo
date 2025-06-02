function ordenarPor(campo) {
    const form = document.getElementById('form_ordenacion');
    const actual = document.getElementById('ordenado_por').value;
    const orden = document.getElementById('orden').value;

    document.getElementById('ordenado_por').value = campo;
    document.getElementById('orden').value = (actual === campo && orden === 'asc') ? 'desc' : 'asc';
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
                document.getElementById('form_ordenacion').submit();
            }
        });
    });
});

window.ordenarPor = ordenarPor;

