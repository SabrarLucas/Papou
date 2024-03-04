import React, {useState, useEffect} from 'react';
import axios from 'axios';
import dayjs from 'dayjs';


const Product = ({supplier}) => {

    const [products, setProducts] = useState([]);


    useEffect(() => {
        fetchProducts();
    }, []);

    const fetchProducts = () => {
        axios.get('http://127.0.0.1:8000/api/product', {
            headers: {
              Accept: "application/json"
            }
          })
            .then((response) => {
                setProducts(response.data);
            })
            .catch((error) => {
                console.error(error);
            });
    };

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
    
    const filteredProducts = supplier != "null" ? products.filter(product => product.company_name === supplier) : products;

    return(
        <table className="product-table-react-component">
            <thead>
                <tr>
                    <th>Catégorie</th>
                    <th>Partenaire</th>
                    <th>Nom</th>
                    <th>Prix</th>
                    <th>Date d'ajout</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                {filteredProducts.map((product) => (
                    <tr key={product.id}>
                        <td>{product.category_name}</td>
                        <td>{product.company_name}</td>
                        <td>{product.name}</td>
                        <td>{product.price}</td>
                        <td>{dayjs(product.createdAt).format("DD/MM/YYYY")}</td>
                        <td>
                            <button onClick={() => handleDelete(product.id)} className='redbtn'>DELETE</button>
                        </td>
                    </tr>
                ))}
            </tbody>
        </table>
    )
}

export default Product;