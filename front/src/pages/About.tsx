import { Outlet, Link } from 'react-router-dom';
import styles from './About.module.css';
//import {route} from '@/utils/router';

// import React from "react";
// import { useLocation, useRoutes } from "react-router-dom";

const About = () => {
    return (
        <div className={styles.about}>
            <h1>About us</h1>
            <p>This is a demo website about React-router-dom library.</p>
            <ul>
                <li><Link to="/about/contacts">Our Contacts</Link></li>
                <li><Link to="/">Our Team</Link></li>
            </ul>

            {/* <Routes>
                <Route path="contacts" element={<p>Our contact</p>} />
                <Route path="team" element={<p>Our team</p>} />
            </Routes> */}
            <Outlet />
        </div>
    )
}

export {About}
