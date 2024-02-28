import axios from "axios";
import React, { useEffect, useState } from "react";

const Filter = () => {
    const [suppliers, setSuppliers] = useState([]);

    useEffect(() => {
        axios.get('http://127.0.0.1:8000/api/suppliers')
            .then((r) => setSuppliers(r.data))
            .catch((e) => console.error(e))
    },[]);
    console.log(suppliers)
    return (
        <div>
            {suppliers.map((supplier) => {
                return(
                    <div key={supplier.id}>
                        <label htmlFor={supplier.companyName}>{supplier.companyName}</label>
                        <input type="checkbox" name={supplier.companyName} />
                    </div>
                ) 
            })}
        </div>
    )
}

export default Filter;