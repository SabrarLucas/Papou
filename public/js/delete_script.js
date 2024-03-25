document.addEventListener('DOMContentLoaded', function() {
    var deleteLink = document.getElementById('delete-account');

    if (deleteLink) {
        deleteLink.addEventListener('click', function(event) {
            event.preventDefault();
            // Afficher la boîte de dialogue personnalisée
            var dialog = document.getElementById('custom-dialog');
            dialog.style.display = 'block';
        });
    }

    var confirmBtn = document.getElementById('confirm-btn');
    var cancelBtn = document.getElementById('cancel-btn');

    confirmBtn.addEventListener('click', function() {
        // Fermer la boîte de dialogue
        var dialog = document.getElementById('custom-dialog');
        dialog.style.display = 'none';
        // Rediriger ou effectuer d'autres actions si nécessaire
        window.location.href = deleteLink.getAttribute('href');
    });

    cancelBtn.addEventListener('click', function() {
        // Fermer la boîte de dialogue
        var dialog = document.getElementById('custom-dialog');
        dialog.style.display = 'none';
    });
});
