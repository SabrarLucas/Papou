import React from "react";
import dayjs from "dayjs";
import Detail from "./Detail";

const Order = ({ ordersPage }) => {
    
    return (
            <table className="order-table-react-component">
                <thead>
                    <tr>
                        <th>Nom du client</th>
                        <th>Commande du</th>
                        <th>Détail</th>
                        <th>total commande sans livraison</th>
                        <th>total pour le partenaire</th>
                        <th>total com papou</th>
                    </tr>
                </thead>
                <tbody>
                    {ordersPage.map((order) => (
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
                            <td>{order.CAPartner} €</td>
                            <td>{order.CAPapou} €</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        
    );
}

export default Order;
