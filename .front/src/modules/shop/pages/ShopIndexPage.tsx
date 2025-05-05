import React from "react";
import { Link} from "react-router-dom";
import {route} from "api";

export const ShopIndexPage: React.FC = ({}) => {
    return (
        <ul>
            <li><Link to={route('shop.articles')}>Статьи</Link></li>
        </ul>
    );
};

