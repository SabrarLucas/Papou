import React, {useState} from "react";
import Filter from "./Filter";
import Order from "./Order";

const App = () => {

    const [supplier, setSupplier] = useState("null")

    const handleChange = (supplierName) => {
        setSupplier(supplierName);
    }

    return(
        <div>
            <Filter onChange={handleChange}/>
            <Order supplier={supplier}/>
        </div>
    );
}

export default App;