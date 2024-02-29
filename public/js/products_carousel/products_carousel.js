document.addEventListener("DOMContentLoaded", function() {
    var carousels = document.querySelectorAll(".products-age-carousel");
    
    carousels.forEach(function(carousel) {
        var container = carousel.querySelector(".products-age-carousel-container");
        var leftButton = carousel.querySelector(".btn-swipe-left");
        var rightButton = carousel.querySelector(".btn-swipe-right");
        
        var nbr = 4;
        var p = 0;

        container.style.width = (15.5 * nbr) + "vw";

        // Ajout des fonctionnalités pour les boutons de défilement
        leftButton.addEventListener("click", function() {
            if (p > 0) {
                p--;
                container.style.transform = "translateX(-" + (15 * p) + "vw)";
                container.style.transition = "all 0.5s ease";
            }
        });

        rightButton.addEventListener("click", function() {
            if (p < nbr - 1) {
                p++;
                container.style.transform = "translateX(-" + (15 * p) + "vw)";
                container.style.transition = "all 0.5s ease";
            }
        });
    });
});
