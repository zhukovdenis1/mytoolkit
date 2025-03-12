import React from 'react';
import { Editor } from "ui";

interface NodeProps {
    id: number;
    name: string;
    text?: string;
    children?: NodeProps[];
}

export const NodeEditor = ({ node }: { node: NodeProps }) => {
    const editor = Editor.useEditor();

    // Устанавливаем значение редактора
    React.useEffect(() => {
        if (editor && node.text) {
            editor.setValue(node.text);
        }
    }, [editor, node.text]);

    if (!node.text) return (<></>);

    return (
        <>
            <Editor editor={editor} mode="view" />
        </>
    );
};
