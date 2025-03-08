import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { api, route } from "api";
import {Spin, Editor, Button, Space, message} from "ui";
import dayjs from "dayjs";
import { useBreadCrumbs } from "@/components/BreadCrumbs";
import {SubNoteList} from "../components/SubNoteList";
import '@/css/notes/detail.css';
import { UndoOutlined, RedoOutlined, EyeOutlined, EditOutlined} from "@ui/icons"

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
    const [showSaveButton, setShowSaveButton] = useState(false);
    const [reset, setReset] = useState(0);
    const [editorMode, setEditorMode] = useState('view');
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
    }, [noteId, reset]);

    const saveChanges = async () => {
        setLoading(true)
        const response = await api.safeRequest(
            `notes.editContent`,
            { note_id: noteId, text: editor.getValue()}
        );
        if (response && typeof response !== 'boolean' && response.data) {
            if (response.data.success) {
                message.success('Data saved successfully');
            } else {
                message.error('Data was')
            }
        }
        setLoading(false)
        setShowSaveButton(false)
    }

    const switchEditorMode = () => {
        setEditorMode(editorMode == 'view' ? 'edit' : 'view')
    }

    return (
        <Spin spinning={loading}>
            <h1>{data?.title}</h1>

            <p className="date">
                {data?.created_at ? dayjs(data.created_at).format("DD.MM.YYYY") : ""}
                (изменено: {data?.updated_at ? dayjs(data.updated_at).format("DD.MM.YYYY") : ""})
            </p>
            <SubNoteList parentId={noteId}/>
            <Editor editor={editor} disabled={loading} mode={editorMode} onChange={() => setShowSaveButton(true)} />

            <div className="stickyBottom" style={{ display: showSaveButton ? '' : 'none' }}>
                <Space>
                    <Button title="Undo" disabled={!editor.isUndoAvailable()} onClick={() => {editor.undo()}}><UndoOutlined /></Button>
                    <Button title="Redo" disabled={!editor.isRedoAvailable()} onClick={() => {editor.redo()}}><RedoOutlined /></Button>
                    <Button title="Mode" type="dashed" onClick={switchEditorMode}>{editorMode == 'view' ? (<EyeOutlined />) : (<EditOutlined />)}</Button>
                    <Button type="primary" onClick={saveChanges}>
                        Save
                    </Button>
                    <Button type="default" onClick={() => setShowSaveButton(false)}>
                        Hide
                    </Button>
                    <Button type="dashed" onClick={() => {setReset(reset + 1)}}>
                        Reset
                    </Button>
                </Space>
            </div>
        </Spin>
    );
};
