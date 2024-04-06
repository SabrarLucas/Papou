import React, { useEffect, useState } from 'react'; // Import des fonctionnalités React
import Product from "./Product"; // Import du composant Product
import ReactPaginate from 'react-paginate'; // Import du composant ReactPaginate pour la pagination
import axios from 'axios'; // Import de la bibliothèque Axios pour les requêtes HTTP

const Pagination = ({ productsPerPage, supplier }) => { // Définition du composant Pagination
    const [products, setProducts] = useState([]); // Déclaration d'un état pour stocker les produits

    const [itemOffset, setItemOffset] = useState(0); // Déclaration d'un état pour gérer le décalage des éléments affichés

    const fetchProducts = () => { // Fonction pour récupérer les produits depuis l'API
        axios.get('http://127.0.0.1:8000/api/product', { // Requête GET pour récupérer la liste des produits depuis l'API
            headers: {
                Accept: "application/json" // Spécification de l'acceptation du format JSON
            }
        })
        .then((response) => {
            setProducts(response.data); // Mise à jour de l'état avec les données récupérées depuis l'API
        })
        .catch((error) => {
            console.error(error); // Gestion des erreurs
        });
    };

    useEffect(() => { // Effet pour exécuter fetchProducts au chargement initial du composant
        fetchProducts();
    }, []);

    const filteredProducts = supplier !== "null" ? products.filter(product => product.company_name === supplier) : products; // Filtrage des produits en fonction du fournisseur sélectionné

    const endOffset = itemOffset + productsPerPage; // Calcul de la fin de la plage des produits à afficher
    const productsPage = filteredProducts.slice(itemOffset, endOffset); // Récupération des produits à afficher sur la page courante
    const pageCount = Math.ceil(filteredProducts.length / productsPerPage); // Calcul du nombre total de pages en fonction du nombre de produits par page

    const handlePageClick = (event) => { // Fonction pour gérer le changement de page
        const newOffset = (event.selected * productsPerPage) % filteredProducts.length; // Calcul du nouvel offset en fonction de la page sélectionnée
        setItemOffset(newOffset); // Mise à jour de l'état avec le nouvel offset
    };

    return (
        <div>
            <Product productsPage={productsPage} /> {/* Affichage des produits sur la page courante */}

            <div className="admin-index-products-pagination"> {/* Pagination */}
            {/* Affichage de la pagination si le nombre de produits dépasse le nombre de produits par page */}
                {filteredProducts.length > productsPerPage ? (
                    <ReactPaginate
                        breakLabel="..." // Libellé pour les éléments de pagination intermédiaires
                        nextLabel="suivant >" // Libellé pour le bouton de page suivante
                        onPageChange={handlePageClick} // Fonction de rappel pour gérer le changement de page
                        pageRangeDisplayed={5} // Nombre maximum de pages à afficher dans la pagination
                        pageCount={pageCount} // Nombre total de pages
                        previousLabel="< précédent" // Libellé pour le bouton de page précédente
                        renderOnZeroPageCount={null} // Rendu nul si le nombre total de pages est zéro
                    />
                ) : (
                    <></> // Rendu vide si le nombre de produits ne dépasse pas le nombre de produits par page
                )}
            </div>
        </div>
    );
}

export default Pagination; // Export du composant Pagination
