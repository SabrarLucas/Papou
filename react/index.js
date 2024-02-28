import React from 'react'
import ReactDOM from 'react-dom/client'
import Order from './compotent/Order'
import Filter from './compotent/Filter'

ReactDOM.createRoot(document.getElementById('react-app')).render(
    <React.StrictMode>
        <Filter />
        <Order />
    </React.StrictMode>,
)