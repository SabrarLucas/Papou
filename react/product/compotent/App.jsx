import React, {useState} from "react";
import Filter from "./Filter";
import Product from "./Product";

const App = () => {

    const [supplier, setSupplier] = useState(null)

    const handleChange = (supplierName) => {
        setSupplier(supplierName);
    }

    return(
        <div>
            <Filter onChange={handleChange}/>
            <Product supplier={supplier}/>
        </div>
    );
}

export default App;