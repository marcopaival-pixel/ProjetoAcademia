<script>
    /**
     * Máscara automática para Telefone/Celular
     * Formatos: (11) 99999-9999 ou (11) 9999-9999
     */
    /**
     * Retorna o valor formatado como Telefone
     */
    window.formatPhone = function(v) {
        if (!v) return '';
        v = v.replace(/\D/g, '').substring(0, 11);
        if (v.length === 0) return '';
        if (v.length <= 2) return '(' + v;
        if (v.length <= 6) return '(' + v.substring(0, 2) + ') ' + v.substring(2);
        if (v.length <= 10) return '(' + v.substring(0, 2) + ') ' + v.substring(2, 6) + '-' + v.substring(6);
        return '(' + v.substring(0, 2) + ') ' + v.substring(2, 7) + '-' + v.substring(7);
    };

    window.maskPhone = function(input) {
        if (!input) return;
        input.value = window.formatPhone(input.value);
    };

    /**
     * Máscara para CPF: 000.000.000-00
     */
    window.maskCpf = function(input) {
        if (!input) return;
        let v = input.value.replace(/\D/g, '').substring(0, 11);
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        input.value = v;
    };

    /**
     * Máscara para CNPJ: 00.000.000/0000-00
     */
    window.maskCnpj = function(input) {
        if (!input) return;
        let v = input.value.replace(/\D/g, '').substring(0, 14);
        v = v.replace(/^(\d{2})(\d)/, '$1.$2');
        v = v.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
        v = v.replace(/(\d{4})(\d)/, '$1-$2');
        input.value = v;
    };

    /**
     * Máscara para CEP: 00000-000
     */
    window.maskCep = function(input) {
        if (!input) return;
        let v = input.value.replace(/\D/g, '').substring(0, 8);
        v = v.replace(/(\d{5})(\d)/, '$1-$2');
        input.value = v;
    };

    // Inicialização automática para campos que já possuem dados (ao carregar a página)
    document.addEventListener('DOMContentLoaded', () => {
        // Encontra campos de telefone pelo name ou id e aplica a máscara inicial
        const phoneInputs = document.querySelectorAll('input[name*="phone"], input[name*="whatsapp"], input#phone, input.mask-phone');
        phoneInputs.forEach(input => {
            if (input.value) window.maskPhone(input);
            // Adiciona o listener se ainda não tiver (opcional se usar oninput no HTML, mas aqui garante)
            input.addEventListener('input', () => window.maskPhone(input));
        });
    });
</script>
