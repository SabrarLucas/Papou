import axios from "axios";
import React, { useEffect, useState } from "react";

const Filter = ({onChange}) => {
    const [suppliers, setSuppliers] = useState([]);

    useEffect(() => {
        axios.get('http://127.0.0.1:8000/api/suppliers', {
            headers: {
              Accept: "application/json"
            }
          })
            .then((r) => setSuppliers(r.data))
            .catch((e) => console.error(e))
    },[]);

    const handleChange = (e) => {
        onChange(e.target.value);
    }

    return (
        <form onChange={handleChange}>
            {suppliers.map((supplier) => {
                return(
                    <>
                        <label htmlFor={supplier.companyName}>{supplier.companyName}</label>
                        <input type="radio" name="group" id={supplier.companyName} value={supplier.companyName}/>
                    </>
                ) 
            })}
        </form>
    );
}

export default Filter;