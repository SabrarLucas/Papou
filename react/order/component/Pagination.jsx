import React, {useEffect, useState} from 'react';
import ReactPaginate from 'react-paginate';
import axios from 'axios';
import Order from './Order';

const Pagination = ({ordersPerPage, supplier}) => {
  const [orders, setOrders] = useState([]);

    const [itemOffset, setItemOffset] = useState(0);

    const fetchOrder = async () => {
      try {
          const response = await axios.get('http://127.0.0.1:8000/api/orders', {
              headers: {
                  Accept: "application/json"
              }
            });
          setOrders(response.data);
      } catch (error) {
          console.error(error);
      }
  };

  useEffect(() => {
      fetchOrder();
  }, []);


  const filteredOrders = supplier != "null" ? orders.filter(order => order.supplierName === supplier) : orders;

    // Simulate fetching items from another resources.
    // (This could be items from props; or items loaded in a local state
    // from an API endpoint with useEffect and useState)
    const endOffset = itemOffset + ordersPerPage;
    const ordersPage = filteredOrders.slice(itemOffset, endOffset);
    const pageCount = Math.ceil(filteredOrders.length / ordersPerPage);

    // Invoke when user click to request another page.
    const handlePageClick = (event) => {
        const newOffset = (event.selected * ordersPerPage) % filteredOrders.length;
        setItemOffset(newOffset);
    };

    return (
        <div>
          <Order ordersPage={ordersPage} />

          {filteredOrders.length > ordersPerPage  ? (
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
        </div>
      );
}

export default Pagination;