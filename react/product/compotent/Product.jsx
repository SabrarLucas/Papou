import React, { useState, useEffect } from 'react'; // Import des fonctionnalités React
import axios from 'axios'; // Import de la bibliothèque Axios pour les requêtes HTTP
import dayjs from 'dayjs'; // Import de la bibliothèque Day.js pour manipuler les dates

const Product = ({ productsPage }) => { // Définition du composant Product
    // Fonction pour gérer la suppression d'un produit
    const handleDelete = (productId) => {
        axios.delete(`http://127.0.0.1:8000/api/products/${productId}`, { // Requête DELETE à l'API pour supprimer le produit
            headers: {
                Accept: "application/ld+json" // Spécification de l'acceptation du format JSON-LD
            }
        })
        .then(() => {
            // Actualiser la liste des produits après la suppression
            fetchProducts();
        })
        .catch((error) => {
            console.error(error); // Gestion des erreurs
        });
    };

    return (
        <table className="product-table-react-component"> {/* Tableau pour afficher les produits */}
            <thead> {/* Entêtes de colonnes */}
                <tr>
                    <th>Nom</th>
                    <th>Partenaire</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Date d'ajout</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody> {/* Corps du tableau */}
            {/* Itération sur les produits */}
                {productsPage.map((product) => (
                    <tr key={product.id}> {/* Ligne pour chaque produit */}
                        <td><a href={"http://127.0.0.1:8000/detail/" + product.id}>{product.name}</a></td> {/* Nom du produit avec lien vers les détails */}
                        <td>{product.company_name}</td> {/* Nom de l'entreprise partenaire */}
                        <td>{product.category_name}</td> {/* Nom de la catégorie du produit */}
                        <td>{product.price}</td> {/* Prix du produit */}
                        <td>{dayjs(product.createdAt).format("DD/MM/YYYY")}</td> {/* Date d'ajout du produit */}
                        <td>
                            {/* Bouton pour voir les détails du produit */}
                            <a href={"http://127.0.0.1:8000/detail/" + product.id} className="btn btn-info">DETAIL</a>
                            {/* Bouton pour supprimer le produit */}
                            <button onClick={() => handleDelete(product.id)} className='btn btn-delete'>SUPPRIMER</button>
                        </td>
                    </tr>
                ))}
            </tbody>
        </table>
    )
}

export default Product; // Export du composant Product
