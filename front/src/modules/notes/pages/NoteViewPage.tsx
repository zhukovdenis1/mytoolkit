import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { api, route } from "api";
import { Spin, Editor } from "ui";
import dayjs from "dayjs";
import { useBreadCrumbs } from "@/components/BreadCrumbs";
import {SubNoteList} from "../components/SubNoteList";
import '@/css/notes/detail.css';

interface NoteData {
    id: number;
    title: string;
    text: string;
    created_at: string;
    updated_at: string;
}

export const NoteViewPage: React.FC = () => {
    const { note_id: noteId } = useParams<{ note_id: string }>();
    const [data, setData] = useState<NoteData | null>(null);
    const [loading, setLoading] = useState(true);
    const editor = Editor.useEditor();
    const brcr = useBreadCrumbs();

    useEffect(() => {
        const fetchData = async () => {
            if (!noteId) return; // Проверка на undefined

            const noteResponse = await api.safeRequest(`notes.show`, { note_id: noteId });
            if (noteResponse && typeof noteResponse !== 'boolean' && noteResponse.data) {
                const noteData = noteResponse.data.data;
                setData(noteData);
                editor.setValue(noteData.text);
                brcr.removeLast();
                brcr.add(noteData.title, route('notes.view', { note_id: noteId })); // Теперь noteId точно string
            }
            setLoading(false);
        };

        fetchData();
    }, [noteId]);

    return (
        <Spin spinning={loading}>
            <h1>{data?.title}</h1>
            <p className="date">
                {data?.created_at ? dayjs(data.created_at).format("DD.MM.YYYY") : ""}
                (изменено: {data?.updated_at ? dayjs(data.updated_at).format("DD.MM.YYYY") : ""})
            </p>
            <SubNoteList parentId={noteId}/>
            <Editor editor={editor} disabled={loading} mode="view"/>
            <button onClick={() => {window.history.pushState(null, "", "/");}}>button</button>
        </Spin>
    );
};
