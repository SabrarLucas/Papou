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
        <div>
          <Product productsPage={productsPage} />

          {filteredProducts.length > productsPerPage  ? (
            <nav aria-label="Page navigation example" className='flex justify-center mt-4'>
              <ReactPaginate
                containerClassName={"inline-flex -space-x-px text-sm"}
                pageClassName={"flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700"}
                activeClassName={"flex items-center justify-center px-3 h-8 leading-tight border-gray-300 bg-blue-50  border border-gray-300 hover:bg-gray-100 hover:text-gray-700"}
                breakClassName={'flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700'}
                previousClassName={"flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700"}
                nextClassName={"flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700"}
                breakLabel="..."
                nextLabel="next >"
                onPageChange={handlePageClick}
                pageRangeDisplayed={5}
                pageCount={pageCount}
                previousLabel="< previous"
                renderOnZeroPageCount={null}
              />
            </nav>
          ):
          (<></>)
          }
        </div>
      );
}

export default Pagination;