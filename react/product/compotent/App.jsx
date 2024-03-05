import React, {useEffect, useState} from "react";
import Filter from "./Filter";
import Pagination from "./Pagination";

const App = () => {

    const [supplier, setSupplier] = useState("null")

    const handleChange = (supplierName) => {
        setSupplier(supplierName);
    }

    return(
        <div className="product-app-react-content">
            <Filter onChange={handleChange}/>
            <Pagination supplier={supplier} productsPerPage={10} />
        </div>
    );
}

export default App;