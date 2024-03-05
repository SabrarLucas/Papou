import axios from "axios";
import React, { useEffect, useState } from "react";
import dayjs from "dayjs";
import Detail from "./Detail";

const Order = ({ supplier }) => {
    const [orders, setOrders] = useState([]);

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

    return (
            <table className="order-table-react-component">
                <thead>
                    <tr>
                        <th>Nom du client</th>
                        <th>Commande du</th>
                        <th>Détail</th>
                        <th>total commande sans livraison</th>
                        <th>total pour le partenaire</th>
                        <th>total com papouuuuuu</th>
                    </tr>
                </thead>
                <tbody>
                    {filteredOrders.map((order) => (
                        <tr key={order.id}>
                            <td>{order.userLastname} {order.userFirstname}</td>
                            <td>{dayjs(order.createdAt).format("DD/MM/YYYY")}</td>
                            <td>
                                <ul>
                                    {order.details.map((detail) => (
                                        <Detail key={detail.id} details={detail}/>
                                    ))}
                                </ul>
                            </td>
                            <td>{order.total} €</td>
                            <td>par la suite</td>
                            <td>par la suite</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        
    );
}

export default Order;
