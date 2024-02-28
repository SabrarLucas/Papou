import React, { useState, useEffect } from "react";
import axios from "axios";

const Detail = ({details}) => {
    const [detail, setDetail] = useState([]);

    useEffect(() => {
        axios.get('http://127.0.0.1:8000' + details)
            .then((r) => setDetail(r.data))
            .catch((e) => console.error(e))
    },[]);

    return(
        <li>{detail.nameProduct}</li>
    )
}

export default Detail;