import axios from "axios"; // Import de la bibliothèque Axios pour les requêtes HTTP
import React, { useEffect, useState } from "react"; // Import des fonctionnalités React

const Filter = ({ onChange }) => { // Définition du composant Filter
    const [suppliers, setSuppliers] = useState([]); // Déclaration d'un état pour stocker les fournisseurs

    useEffect(() => { // Effet de chargement des fournisseurs au chargement du composant
        axios.get('http://127.0.0.1:8000/api/suppliers', { // Requête GET pour récupérer la liste des fournisseurs depuis l'API
            headers: {
                Accept: "application/json" // Spécification de l'acceptation du format JSON
            }
        })
        .then((r) => setSuppliers(r.data)) // Mise à jour de l'état avec les données récupérées depuis l'API
        .catch((e) => console.error(e)) // Gestion des erreurs
    }, []); // La dépendance vide indique que l'effet doit être exécuté une seule fois, au chargement initial du composant

    const handleChange = (e) => { // Fonction pour gérer le changement de sélection
        onChange(e.target.value); // Appel de la fonction onChange avec la valeur sélectionnée comme argument
    }

    const handleClick = () => { // Fonction pour gérer le clic sur le bouton de suppression du filtre
        onChange("null"); // Appel de la fonction onChange avec la valeur "null" pour supprimer le filtre
    }

    return (
        <form onChange={handleChange} className="product-filter-react-form"> {/* Formulaire pour filtrer les fournisseurs */}
            <div className="product-filter-react-title"> {/* Titre du formulaire */}
                <h1>Filtrer par partenaire</h1>
            </div>
            {suppliers.map((supplier) => { // Itération sur la liste des fournisseurs pour afficher les options de filtrage
                return(
                    <div className="product-filter-react-content"> {/* Contenu du formulaire */}
                        <label htmlFor={supplier.companyName}>{supplier.companyName}</label> {/* Nom du fournisseur comme étiquette */}
                        <input type="radio" name="group" id={supplier.companyName} value={supplier.companyName}/> {/* Bouton radio pour sélectionner le fournisseur */}
                    </div>
                ) 
            })}
            <div className="flex justify-center"> {/* Bouton pour supprimer le filtre */}
                <button onClick={handleClick} >supprimer le filtre</button>
            </div>
        </form>
    );
}

export default Filter; // Export du composant Filter
