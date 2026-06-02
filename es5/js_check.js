document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM carregado. A verificar Bootstrap...');
    if (typeof bootstrap !== 'undefined') {
        console.log('Bootstrap detetado! Versão: ' + bootstrap.Tooltip.VERSION);
    } else {
        console.error('Bootstrap NÃO detetado!');
    }
    
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl)
    });
    console.log('Dropdowns inicializados manualmente: ' + dropdownList.length);
});
