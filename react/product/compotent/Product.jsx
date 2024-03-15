import React, {useState, useEffect} from 'react';
import axios from 'axios';
import dayjs from 'dayjs';


const Product = ({productsPage}) => {

    const handleDelete = (productId) => {
        axios.delete(`http://127.0.0.1:8000/api/products/${productId}`, {
            headers: {
              Accept: "application/ld+json"
            }
          })
            .then(() => {
                // Actualiser la liste des produits après la suppression
                fetchProducts();
            })
            .catch((error) => {
                console.error(error);
            });
    };

    return(
        <table className="product-table-react-component">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Partenaire</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Date d'ajout</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                {productsPage.map((product) => (
                    <tr key={product.id}>
                        <td><a href={"http://127.0.0.1:8000/detail/" + product.id}>{product.name}</a></td>
                        <td>{product.company_name}</td>
                        <td>{product.category_name}</td>
                        <td>{product.price}</td>
                        <td>{dayjs(product.createdAt).format("DD/MM/YYYY")}</td>
                        <td>
                            <a href={"http://127.0.0.1:8000/detail/" + product.id} className="btn btn-info">DETAIL</a>
                            <button onClick={() => handleDelete(product.id)} className='btn btn-delete'>SUPPRIMER</button>
                        </td>
                    </tr>
                ))}
            </tbody>
        </table>
    )
}

export default Product;