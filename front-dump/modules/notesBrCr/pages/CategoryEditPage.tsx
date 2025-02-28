import React, { useState, useEffect } from "react";
import { useNavigate, useParams } from "react-router-dom";
import api from "../../../services/api";
import { Category } from "../types/category";

const CategoryEditPageOrig: React.FC = () => {
    const { category_id } = useParams<{ category_id: string }>();
    const navigate = useNavigate();

    const [category, setCategory] = useState<Category | null>(null);
    const [name, setName] = useState("");
    const [parentId, setParentId] = useState<number | null>(null);
    const [allCategories, setAllCategories] = useState<Category[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [successMessage, setSuccessMessage] = useState<string | null>(null);

    useEffect(() => {
        const fetchCategoryData = async () => {
            setLoading(true);
            try {
                // Fetch category by ID
                const categoryResponse = await api.get(`/note/categories/${category_id}`);
                setCategory(categoryResponse.data.noteCategory);
                setName(categoryResponse.data.noteCategory.name);
                setParentId(categoryResponse.data.noteCategory.parentId);

                // Fetch all categories for select options
                const categoriesResponse = await api.get("/note/categories/all");
                setAllCategories(categoriesResponse.data.data);
            } catch (err) {
                setError("Error loading data.");
            } finally {
                setLoading(false);
            }
        };

        fetchCategoryData();
    }, [category_id]);

    const handleSave = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);

        try {
            const response = await api.put(`/note/categories/${category_id}`, {
                name,
                parent_id: parentId,
            });

            setSuccessMessage("Category updated successfully!");
            setTimeout(() => {
                navigate(`/notes/category/${parentId || "null"}`);
            }, 2000);
        } catch (err) {
            setError("Failed to update category.");
        } finally {
            setLoading(false);
        }
    };

    if (loading) return <div>Loading...</div>;

    return (
        <div>
            <h1>Edit Category</h1>
            {error && <div style={{ color: "red" }}>{error}</div>}
            {successMessage && <div style={{ color: "green" }}>{successMessage}</div>}
            {category && (
                <form onSubmit={handleSave}>
                    <div>
                        <label htmlFor="name">Name</label>
                        <input
                            id="name"
                            type="text"
                            value={name}
                            onChange={(e) => setName(e.target.value)}
                        />
                    </div>
                    <div>
                        <label htmlFor="parentId">Parent Category</label>
                        <select
                            id="parentId"
                            value={parentId || ""}
                            onChange={(e) => setParentId(e.target.value ? Number(e.target.value) : null)}
                        >
                            <option value="">None</option>
                            {allCategories.map((cat) => (
                                <option key={cat.id} value={cat.id}>
                                    {cat.name}
                                </option>
                            ))}
                        </select>
                    </div>
                    <div>
                        <button type="submit">Save</button>
                        <button
                            type="button"
                            onClick={() => navigate(`/notes/category/${parentId || "null"}`)}
                        >
                            Save and Exit
                        </button>
                    </div>
                </form>
            )}
        </div>
    );
};

export default CategoryEditPage;
