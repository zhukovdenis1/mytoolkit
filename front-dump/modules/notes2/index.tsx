import React from "react";
import { Routes, Route } from "react-router-dom";
import CategoriesPage from "./pages/CategoriesPage";
import EditCategoryPage from "./pages/EditCategoryPage";

const Notes2Module: React.FC = () => {
    return (
        <Routes>
            <Route path="/" element={<CategoriesPage />} />
            <Route path="/add" element={<EditCategoryPage />} />
            <Route path="/edit/:id" element={<EditCategoryPage />} />
        </Routes>
    );
};

export default Notes2Module;
