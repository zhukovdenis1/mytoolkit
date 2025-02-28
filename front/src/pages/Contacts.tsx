import { Outlet } from 'react-router-dom';
// import styles from './About.module.css';
// import {route} from '@/utils/router';

//import React from "react";

const Contacts = () => {
    return (
        <div>
            <h1>Contacts</h1>


            <Outlet />
        </div>
    )
}

export {Contacts}
