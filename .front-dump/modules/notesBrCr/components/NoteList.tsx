import React, { useEffect, useState } from "react";
import { useSearchParams, Link } from "react-router-dom";
import api from "../services/noteApi";
import { Note } from "../types/note";
import { Loading } from "@/components/Loading";
import {ROUTES} from "../routes.ts";

const NoteList = ({ categoryId }: { categoryId: number | null }) => {
    const [notes, setNotes] = useState<Note[]>([]);
    const [searchParams] = useSearchParams();
    const [isLoading, setIsLoading] = useState<boolean>(true);

    useEffect(() => {
        const fetchNotes = async () => {
            const urlCategoryId = searchParams.get("category");
            const search = searchParams.get("search") || "";

            // Определяем приоритет: categoryId пропса > category из URL
            const effectiveCategoryId = categoryId !== null ? categoryId : urlCategoryId || "";

            try {
                const response = await api.get(`/notes`, {
                    params: { category: effectiveCategoryId, search },
                });
                setIsLoading(false);
                setNotes(response.data.data);
            } catch (error) {
                console.error("Error fetching notes:", error);
            }
        };

        fetchNotes();
    }, [categoryId, searchParams]); // Обновляем при изменении пропса или GET-параметров

    const handleDelete = async (noteId: number) => {
        if (window.confirm("Are you sure?")) {
            try {
                await api.delete(`/notes/${noteId}`);
                setNotes((prevNotes) => prevNotes.filter((note) => note.id !== noteId));
                alert("Note deleted");
            } catch (err) {
                alert("Error deleting note");
            }
        }
    };

    return (
        <div>
            <h3>Notes</h3>
            {isLoading ? ( // Проверяем, идет ли загрузка
                <Loading />
            ) : (
                notes.length ? (
                    <ul className="list">
                        {notes.map((note) => (
                            <li key={note.id}>
                                <Link to={`/note-${note.id}/edit`}>{note.title}</Link>
                                <button
                                    onClick={() => handleDelete(note.id)}
                                    className="delete-btn"
                                >
                                    Delete
                                </button>
                            </li>
                        ))}
                        <li><Link to="/notes/add">Add Note</Link></li>
                    </ul>
                ) : (<Link to="/add">Add Note</Link>)
            )}

        </div>
    );
};

export default NoteList;
