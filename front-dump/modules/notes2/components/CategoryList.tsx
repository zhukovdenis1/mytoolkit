import React from "react";
import { useAppDispatch, useAppSelector } from "../../../store/hooks";
import { loadCategories, removeCategory } from "../store/categorySlice";
import { Link } from "react-router-dom";

const CategoryList: React.FC = () => {
    const dispatch = useAppDispatch();
    const { categories, loading, error } = useAppSelector((state) => state.categories);

    React.useEffect(() => {
        dispatch(loadCategories());
    }, [dispatch]);

    const handleDelete = (id: number) => {
        dispatch(removeCategory(id));
    };

    if (loading) return <div>Loading...</div>;
    if (error) return <div>Error: {error}</div>;

    if (!categories || categories.length === 0) {
        return <p>No categories available.</p>;
    }

    return (
        <ul>
            {categories.map((category) => (
                <li key={category.id} id={`category/${category.id}`}>
                    {category.name}{" "}
                    <Link to={`/notes2/edit/${category.id}`}>[edit]</Link>{" "}
                    <button onClick={() => handleDelete(category.id)}>[del]</button>
                </li>
            ))}
        </ul>
    );
};

export default CategoryList;
