import axios from "axios";
import React, { useEffect, useState } from "react";
import dayjs from "dayjs";
import Detail from "./Detail";

const Order = () => {
    const [orders, setOrders] = useState([]);

    useEffect(() => {
        axios.get('http://127.0.0.1:8000/api/orders')
            .then((r) => setOrders(r.data))
            .catch((e) => console.error(e))
    },[]);

    return(
        <div>
            <table>
                <thead>
                    <tr>
                        <th>Nom du client</th>
                        <th>Commande du</th>
                        <th>Détail</th>
                        <th>total commande sans livraison</th>
                        <th>total pour le partnaire</th>
                        <th>total com papou</th>
                    </tr>
                </thead>
                <tbody>
                    {orders.map((order) => {
                        return(
                            <tr key={order.id}>
                                <td>{order.userLastname} {order.userFirstname}</td>
                                <td>{dayjs(order.createdAt).format("DD/MM/YYYY")}</td>
                                <td><ul>{order.details.map((detail) => {
                                    return (
                                        <Detail details={detail}/>
                                    )
                                })}</ul></td>
                                <td>{order.total} €</td>
                                <td>par la suite</td>
                                <td>par la suite</td>
                            </tr>
                        )
                    })}
                </tbody>
            </table>
        </div>
    );
}

export default Order;