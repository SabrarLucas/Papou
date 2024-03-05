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

    // Simulate fetching items from another resources.
    // (This could be items from props; or items loaded in a local state
    // from an API endpoint with useEffect and useState)
    const endOffset = itemOffset + productsPerPage;
    const productsPage = filteredProducts.slice(itemOffset, endOffset);
    const pageCount = Math.ceil(filteredProducts.length / productsPerPage);

    // Invoke when user click to request another page.
    const handlePageClick = (event) => {
        const newOffset = (event.selected * productsPerPage) % filteredProducts.length;
        setItemOffset(newOffset);
    };

    return (
        <>
          <Product productsPage={productsPage} />

          {filteredProducts.length > productsPerPage  ? (
            <ReactPaginate
              breakLabel="..."
              nextLabel="next >"
              onPageChange={handlePageClick}
              pageRangeDisplayed={5}
              pageCount={pageCount}
              previousLabel="< previous"
              renderOnZeroPageCount={null}
            />

          ):
          (<></>)
          }
        </>
      );
}

export default Pagination;