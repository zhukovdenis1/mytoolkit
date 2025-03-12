import React, { useEffect, useState } from "react";
import api from "../services/noteApi";
import { Link } from "react-router-dom";
import { Category } from "../types/category";
import { Loading } from "@/components/Loading";
import { NotesRoute } from "../routes.ts";

const CategoryList = ({ parentId }: { parentId: number | null }) => {
    const [categories, setCategories] = useState<Category[]>([]);
    const [isLoading, setIsLoading] = useState<boolean>(true);

    const getParentId = parentId || '';

    useEffect(() => {
        api
            .get(`/note/categories?parent_id=${getParentId}`)
            .then((response) => setCategories(response.data.data))
            .catch((error) => console.error(error))
            .finally(() => setIsLoading(false));
    }, [parentId]);


    return (
        <>
            <h3>Categories</h3>

            {isLoading ? ( // Проверяем, идет ли загрузка
                    <Loading />
                ) : (
                categories.length ? (
                        <ul className="list">
                    {categories.map((category) => (
                        <li  key={category.id}>
                            <span><Link to={`/notes/category/${category.id}/`}>{category.name}</Link></span>
                            <div className="buttons">
                                <button
                                    onClick={() => handleDelete(category.id)}
                                    className="delete-btn"
                                >
                                    Delete
                                </button>
                                <Link to={`category/${category.id}/edit`}>Edit</Link>
                            </div>
                        </li>
                    ))}
                    <li><Link to={NotesRoute.category.add}>Add Category</Link></li>
                </ul>
                ) : (<Link to={NotesRoute.category.add}>Add Category</Link>)
            )}

        </>
    );

    function handleDelete(categoryId: number) {
        if (window.confirm("Are you sure?")) {
            api
                .delete(`/note/categories/${categoryId}`)
                .then(() => alert("Category deleted"))
                .catch((err) => alert("Error deleting category"));
        }
    }
};

export default CategoryList;
