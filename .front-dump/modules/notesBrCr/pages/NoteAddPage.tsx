import React, { useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import api from "../services/noteApi";

const NoteAddPage = () => {
    const { category_id } = useParams();
    const [title, setTitle] = useState("");
    const [text, setText] = useState("");
    const [categories, setCategories] = useState([parseInt(category_id || "0")]);
    const navigate = useNavigate();

    const handleSave = async () => {
        try {
            await api.post("/notes", { title, text, categories });
            alert("Note saved successfully");
            navigate(`/category/${category_id}`);
        } catch (error) {
            alert("Error saving note");
        }
    };

    return (
        <div>
            <h2>Add Note</h2>
            <input
                type="text"
                value={title}
                onChange={(e) => setTitle(e.target.value)}
                placeholder="Title"
            />
            <textarea
                value={text}
                onChange={(e) => setText(e.target.value)}
                placeholder="Note text"
            />
            <button onClick={handleSave}>Save</button>
        </div>
    );
};

export default NoteAddPage;
