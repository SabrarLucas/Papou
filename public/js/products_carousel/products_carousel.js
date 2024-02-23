divproducts=getElementById.innerHTML("products-carousel");
divproducts.onload=function(){
    nbr=4;  // nombre d'images dans le carrousel
    p=0;    // position initiale
    container=getElementById("products-carousel-container");    // récupération du container contenant les images
    g=getElementById("products-carousel-container");    // récupération du bouton de défilement gauche
    d=getElementById("products-carousel-container");    // récupération du bouton de défilement droite
    container.style.width=(80*nbr)+"vw"; // largeur du carrousel
    for( i=1 ; i < 5 ; i++){ // boucle itérative pour le défilement des images
        div=document.createElement("div");  // création d'une div (html dynamique)
        div.className="products-carousel-img"; // class de la div qui est crée
        div.style.backgroundImage="url('images/image"+i+".png')"; // background de la div qui est crée
        container.appendChild(div); // ajout de la div crée dans le conteneur 'products-carousel-container'
    }
}