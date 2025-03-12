import React, {useEffect, useState} from "react";
import { useParams } from "react-router-dom";
import {api, route} from "api";
import {Spin, Editor} from "ui";
import dayjs from "dayjs";
import { useBreadCrumbs } from "@/components/BreadCrumbs";

export const NoteViewPage: React.FC = () => {
    const { note_id: noteId } = useParams<{ note_id: string }>();
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(true);
    const editor = Editor.useEditor();
    const brcr = useBreadCrumbs();

    editor.onChange = () => {
        if (!loading) {
            //alert(editor.getValue())
        }
    }

    useEffect(() => {
        const fetchData = async () => {
            const noteResponse = await api.safeRequest(`notes.show`, {note_id: noteId});
            const data = noteResponse?.data?.data ?? null
            if (data) {
                setData(data)
                editor.setValue(data.text)
                brcr.removeLast();
                brcr.add(data.title, route('notes.view', {note_id: noteId}))
            }
            setLoading(false)
        };
        fetchData();


    }, [noteId]);

    // return <Spin spinning={loading} >
    //     <p dangerouslySetInnerHTML={{__html: editor.decode(data.text)}}/>
    // </Spin>;
    return <>
        <Spin spinning={loading} >
            <h1>{data?.title}</h1>
            <p>Дата:
                {data?.created_at ? dayjs(data.created_at).format("DD.MM.YYYY") : ""}
                (изменено: {data?.created_at ? dayjs(data.updated_at).format("DD.MM.YYYY") : ""})
            </p>
            <Editor editor={editor} disabled={loading} mode="view"/>
        </Spin>
    </>
};
