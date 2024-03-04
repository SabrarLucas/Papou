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

    const handleClick = () => {
        onChange("null");
    }
    return (
        <form onChange={handleChange} className="product-filter-react-form">
            <div className="product-filter-react-title">
                <h1>Filtrer par partenaire</h1>
            </div>
            {suppliers.map((supplier) => {
                return(
                        <div className="product-filter-react-content">
                            <label htmlFor={supplier.companyName}>{supplier.companyName}</label>
                            <input type="radio" name="group" id={supplier.companyName} value={supplier.companyName}/>
                        </div>
                ) 
            })}
            <div className="flex justify-center">
                <button onClick={handleClick} >supprimer le filtre</button>
            </div>

        </form>
    );
}

export default Filter;