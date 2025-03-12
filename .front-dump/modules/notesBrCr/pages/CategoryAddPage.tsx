import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import api from "../services/noteApi";

const CategoryAddPage = () => {
    const [name, setName] = useState("");
    const [parentId, setParentId] = useState<number | null>(null);
    const navigate = useNavigate();

    const handleSave = async () => {
        try {
            await api.post("/note/categories", { name, parent_id: parentId });
            alert("Category saved successfully");
            navigate("/notes");
        } catch (error) {
            alert("Error saving category");
        }
    };

    return (
        <div>
            <h2>Add Category</h2>
            <input
                type="text"
                value={name}
                onChange={(e) => setName(e.target.value)}
                placeholder="Category name"
            />
            <button onClick={handleSave}>Save</button>
        </div>
    );
};

export default CategoryAddPage;
