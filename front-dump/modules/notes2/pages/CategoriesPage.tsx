import React from "react";
import CategoryList from "../components/CategoryList";
import { Link } from "react-router-dom";

const CategoriesPage: React.FC = () => {
    return (
        <div>
            <h1>Categories</h1>
            <Link to="/notes2/add">[add]</Link>
            <CategoryList />
        </div>
    );
};

export default CategoriesPage;
