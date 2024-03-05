import React, {useEffect, useState} from 'react';
import Product from "./Product";
import ReactPaginate from 'react-paginate';
import axios from 'axios';

const Pagination = ({productsPerPage, supplier}) => {
    const [products, setProducts] = useState([]);

    const [itemOffset, setItemOffset] = useState(0);

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

    useEffect(() => {
        fetchProducts();
    }, []);


    const filteredProducts = supplier != "null" ? products.filter(product => product.company_name === supplier) : products;

    const endOffset = itemOffset + productsPerPage;
    const productsPage = filteredProducts.slice(itemOffset, endOffset);
    const pageCount = Math.ceil(filteredProducts.length / productsPerPage);

    const handlePageClick = (event) => {
        const newOffset = (event.selected * productsPerPage) % filteredProducts.length;
        setItemOffset(newOffset);
    };

    return (
        <div>
          <Product productsPage={productsPage} />

          <div className="admin-index-products-pagination">
          {filteredProducts.length > productsPerPage  ? (
            <ReactPaginate
              breakLabel="..."
              nextLabel="suivant >"
              onPageChange={handlePageClick}
              pageRangeDisplayed={5}
              pageCount={pageCount}
              previousLabel="< précédent"
              renderOnZeroPageCount={null}
            />

          ):
          (<></>)
          }
        </>
      );
}

export default Pagination;