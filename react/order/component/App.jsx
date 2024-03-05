import React, {useState} from "react";
import Filter from "./Filter";
import Order from "./Order";
import Pagination from "./Pagination";

const App = () => {

    const [supplier, setSupplier] = useState("null")

    const handleChange = (supplierName) => {
        setSupplier(supplierName);
    }

    return(
        <div className="order-app-react-content">
            <Filter onChange={handleChange}/>
            <Pagination ordersPerPage={10} supplier={supplier}/>
        </div>
    );
}

export default App;