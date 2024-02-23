
        document.addEventListener("DOMContentLoaded", function() {
            var nbr = 4;
            var p = 0;
            var container = document.getElementById("products-carousel-container");
            var g = document.getElementById("btn-swipe-left");
            var d = document.getElementById("btn-swipe-right");
            container.style.width = (80 * nbr) + "vw";

            for (var i = 1; i <= nbr; i++) {
                var div = document.createElement("div");
                div.className = "products-carousel-img";
                div.style.backgroundImage = "url('../images/image" + i + ".png')"; // Correction ici : ajout de "url()"
                container.appendChild(div);
            }

            // Ajout des fonctionnalités pour les boutons de défilement
            g.addEventListener("click", function() {
                if (p > 0) {
                    p--;
                    container.style.transform = "translateX(-" + (80 * p) + "vw)";
                    container.style.transition = "all 0.5s ease"
                }
            });

            d.addEventListener("click", function() {
                if (p < nbr - 1) {
                    p++;
                    container.style.transform = "translateX(-" + (80 * p) + "vw)";
                    container.style.transition = "all 0.5s ease"
                }
            });
        });