import React, { useState, useEffect } from "react";
import { useNavigate, useParams } from "react-router-dom";
import api from "../../../services/api";
import { Category, Note } from "../types";

const NoteEditPage: React.FC = () => {
    const { note_id } = useParams<{ note_id: string }>();
    const navigate = useNavigate();

    const [note, setNote] = useState<Note | null>(null);
    const [categories, setCategories] = useState<Category[]>([]);
    const [selectedCategories, setSelectedCategories] = useState<number[]>([]);
    const [title, setTitle] = useState("");
    const [text, setText] = useState("");
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [successMessage, setSuccessMessage] = useState<string | null>(null);

    useEffect(() => {
        const fetchNoteAndCategories = async () => {
            setLoading(true);
            try {
                // Fetch note by ID
                const noteResponse = await api.get(`/notes/${note_id}`);
                setNote(noteResponse.data.data);
                setTitle(noteResponse.data.data.title);
                setText(noteResponse.data.data.text);
                setSelectedCategories(noteResponse.data.data.categories.map((category: Category) => category.id));

                // Fetch all categories for select options
                const categoriesResponse = await api.get("/note/categories/all");
                setCategories(categoriesResponse.data.data);
            } catch (err) {
                setError("Error loading data.");
            } finally {
                setLoading(false);
            }
        };

        fetchNoteAndCategories();
    }, [note_id]);

    const handleSave = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);

        try {
            const response = await api.put(`/notes/${note_id}`, {
                title,
                text,
                categories: selectedCategories,
            });

            setSuccessMessage("Note updated successfully!");
            setTimeout(() => {
                navigate(`/notes/category/${selectedCategories[0] || "null"}`);
            }, 2000);
        } catch (err) {
            setError("Failed to update note.");
        } finally {
            setLoading(false);
        }
    };

    if (loading) return <div>Loading...</div>;

    return (
        <div>
            <h1>Edit Note</h1>
            {error && <div style={{ color: "red" }}>{error}</div>}
            {successMessage && <div style={{ color: "green" }}>{successMessage}</div>}
            {note && (
                <form onSubmit={handleSave}>
                    <div>
                        <label htmlFor="title">Title</label>
                        <input
                            id="title"
                            type="text"
                            value={title}
                            onChange={(e) => setTitle(e.target.value)}
                        />
                    </div>
                    <div>
                        <label htmlFor="text">Text</label>
                        <textarea
                            id="text"
                            value={text}
                            onChange={(e) => setText(e.target.value)}
                        />
                    </div>
                    <div>
                        <label htmlFor="categories">Categories</label>
                        <select
                            id="categories"
                            value={selectedCategories}
                            onChange={(e) => setSelectedCategories(Array.from(e.target.selectedOptions, option => Number(option.value)))}
                            multiple
                        >
                            {categories.map((category) => (
                                <option key={category.id} value={category.id}>
                                    {category.name}
                                </option>
                            ))}
                        </select>
                    </div>
                    <div>
                        <button type="submit">Save</button>
                        <button
                            type="button"
                            onClick={() => navigate(`/notes/category/${selectedCategories[0] || "null"}`)}
                        >
                            Save and Exit
                        </button>
                    </div>
                </form>
            )}
        </div>
    );
};

export default NoteEditPage;
