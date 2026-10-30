/* JavaScript de CONFORT uniquement. La securite et la validation sont cote serveur (PHP). */
(function () {
    'use strict';

    // 1. Le bouton "retour" ne doit jamais ressortir une page interne du cache.
    window.addEventListener('pageshow', function (ev) {
        if (ev.persisted) { window.location.reload(); }
    });

    // 2. Filtrage instantane des lignes du tableau affiche.
    document.querySelectorAll('[data-filtre-table]').forEach(function (champ) {
        var table = document.querySelector(champ.getAttribute('data-filtre-table'));
        if (!table) { return; }
        champ.addEventListener('input', function () {
            var terme = champ.value.toLowerCase();
            table.querySelectorAll('tbody tr').forEach(function (ligne) {
                ligne.hidden = terme !== '' && ligne.textContent.toLowerCase().indexOf(terme) === -1;
            });
        });
    });

    // 3. Controle immediat d'un champ (n'empeche jamais l'envoi : le serveur re-verifie tout).
    function messageErreur(champ) {
        var v = champ.value.trim(), regle = champ.getAttribute('data-regle');
        if (v === '') { return champ.hasAttribute('data-obligatoire') && !champ.disabled ? 'Ce champ est obligatoire.' : ''; }
        if (regle === 'email' && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(v)) { return 'Adresse électronique invalide (exemple : nom@domaine.fr).'; }
        if (regle === 'telephone' && !/^(?:0|\+33\s?)[1-9](?:[\s.\-]?\d{2}){4}$/.test(v)) { return 'Téléphone invalide (exemple : 06 12 34 56 78).'; }
        if (regle === 'date') {
            var m = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec(v);
            if (!m) { return 'Format attendu : JJ/MM/AAAA.'; }
            var d = new Date(+m[3], +m[2] - 1, +m[1]);
            if (d.getFullYear() !== +m[3] || d.getMonth() !== +m[2] - 1 || d.getDate() !== +m[1]) { return 'Cette date n’existe pas.'; }
        }
        return '';
    }
    document.querySelectorAll('form[novalidate] [data-obligatoire], form[novalidate] [data-regle]').forEach(function (champ) {
        champ.addEventListener('blur', function () {
            var msg = messageErreur(champ), retour = champ.parentNode.querySelector('.invalid-feedback');
            champ.classList.toggle('is-invalid', msg !== '');
            if (retour) { retour.textContent = msg; }
        });
    });

    // 4. Apprenant : l'entreprise n'est utile que pour un alternant.
    var regime = document.getElementById('champ-regime'), ligneEnt = document.getElementById('ligne-entreprise');
    if (regime && ligneEnt) {
        var majEnt = function () { ligneEnt.hidden = regime.value !== 'alternant'; };
        regime.addEventListener('change', majEnt); majEnt();
    }

    // 5. Intervenant : le tarif n'est utile que pour un vacataire.
    var statut = document.getElementById('champ-statut'), tarif = document.getElementById('champ-tarif_horaire');
    if (statut && tarif) {
        var majTarif = function () {
            var vac = statut.value === 'vacataire';
            tarif.disabled = !vac;
            if (!vac) { tarif.value = ''; tarif.classList.remove('is-invalid'); }
        };
        statut.addEventListener('change', majTarif); majTarif();
    }
})();
